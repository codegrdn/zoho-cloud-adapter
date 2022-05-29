<?php
// Copyright (c) Microsoft Corporation.
// Licensed under the MIT License.

namespace App\TokenStore;

use Illuminate\Support\Facades\DB;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;

class TokenCache
{
    public function storeTokens($accessToken, $user)
    {
        DB::table('users')->where('id', $user->id)->update([
            'storage_token' => serialize([
                'accessToken' => $accessToken->getToken(),
                'refreshToken' => $accessToken->getRefreshToken(),
                'tokenExpires' => $accessToken->getExpires(),
            ])
        ]);
    }

    public function clearTokens($user)
    {
        DB::table('users')->where('id', $user->id)->update([
            'storage_token' => null
        ]);
    }

    public function getAccessToken($user)
    {
        // Check if tokens exist
        if (!$user->storage_token) {
            return '';
        }

        $accessToken = unserialize($user->storage_token);

        // Check if token is expired
        //Get current time + 5 minutes (to allow for time differences)
        $now = time() + 300;
        if ($accessToken['tokenExpires'] <= $now) {
            // Token is expired (or very close to it)
            // so let's refresh

            // Initialize the OAuth client
            $oauthClient = new GenericProvider([
                'clientId'                => config('azure.appId'),
                'clientSecret'            => config('azure.appSecret'),
                'redirectUri'             => config('azure.redirectUri'),
                'urlAuthorize'            => config('azure.authority').config('azure.authorizeEndpoint'),
                'urlAccessToken'          => config('azure.authority').config('azure.tokenEndpoint'),
                'urlResourceOwnerDetails' => '',
                'scopes'                  => config('azure.scopes')
            ]);

            try {
                $newToken = $oauthClient->getAccessToken('refresh_token', [
                    'refresh_token' => $accessToken['refreshToken']
                ]);

                // Store the new values
                $this->updateTokens($newToken, $user);

                return $newToken->getToken();
            }
            catch (IdentityProviderException $e) {
                return '';
            }
        }

        // Token is still valid, just return it
        return $accessToken['accessToken'];
    }

    public function updateTokens($accessToken, $user)
    {
        DB::table('users')->where('id', $user->id)->update([
            'storage_token' => serialize([
                'accessToken' => $accessToken->getToken(),
                'refreshToken' => $accessToken->getRefreshToken(),
                'tokenExpires' => $accessToken->getExpires(),
            ])
        ]);
    }
}
