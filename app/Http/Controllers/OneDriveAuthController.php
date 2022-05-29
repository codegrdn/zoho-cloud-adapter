<?php

namespace App\Http\Controllers;

use App\User;
use http\Env\Response;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller as BaseController;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use App\TokenStore\TokenCache;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class OneDriveAuthController extends BaseController
{
    public function auth(Request $request)
    {
        $this->validate($request, [
            'auth_hash' => 'required',
        ]);

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

        $authUrl = $oauthClient->getAuthorizationUrl();

        // Save client state so we can validate in callback
        $_SESSION['oauthState'] = $oauthClient->getState();
        $_SESSION['userAuthHash'] = $request->get('auth_hash');

        // Redirect to AAD signin page
       return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        // Validate state
        $expectedState = $_SESSION['oauthState'];
        $userAuthHash = $_SESSION['userAuthHash'];
        unset($_SESSION['oauthState']);
        unset($_SESSION['userAuthHash']);
        $providedState = $request->query('state');

        if (!isset($expectedState)) {
            // If there is no expected state in the session,
            // do nothing and redirect to the home page.
            throw new RuntimeException('fail to find oauthState into the current session.');
        }

        if (!isset($userAuthHash)) {
            // If there is no expected state in the session,
            // do nothing and redirect to the home page.
            throw new RuntimeException('fail to find auth_hash into the current session.');
        }

        if (!isset($providedState) || $expectedState != $providedState) {
            return redirect('/')
                ->with('error', 'Invalid auth state')
                ->with('errorDetail', 'The provided auth state did not match the expected value');
        }

        // Authorization code should be in the "code" query param
        $authCode = $request->query('code');
        if (isset($authCode)) {
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
                // Make the token request
                $accessToken = $oauthClient->getAccessToken('authorization_code', [
                    'code' => $authCode
                ]);

                $graph = new Graph();
                $graph->setAccessToken($accessToken->getToken());

                $user = DB::table('users')->where('auth_hash', $userAuthHash)->first();

                $tokenCache = new TokenCache();
                $tokenCache->storeTokens($accessToken, $user);

                return response()->json([
                    'status' => true,
                    'message' => 'OneDrive Access Token successfully obtained',
                    'data' => [
                        'userHash' => $user->auth_hash
                    ]
                ]);
            }

            catch (IdentityProviderException $e) {
                return redirect('/')
                    ->with('error', 'Error requesting access token')
                    ->with('errorDetail', json_encode($e->getResponseBody()));
            }
        }

        throw new RuntimeException("There's no authCode in session.");
    }
}