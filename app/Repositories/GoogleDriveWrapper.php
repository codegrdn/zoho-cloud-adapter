<?php

namespace App\Repositories;

use App\User;
use Google_Client;
use Google_Service_Drive;
use Illuminate\Support\Facades\DB;

class GoogleDriveWrapper
{
    public static function initClient(User $user)
    {
        $client = new Google_Client();

        $client->setScopes(Google_Service_Drive::DRIVE);
        $client->setApplicationName('Holistic Dev App');
        $client->setAuthConfig(storage_path('google_drive/credentials.json'));
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        $client->setPrompt('select_account consent');
        $client->setRedirectUri(env('APP_URL') . "/auth/google-drive-callback");

        if ($user->storage_token) {
            $client->setAccessToken($user->storage_token);

            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());

                DB::table('users')->where('id', $user->id)->update([
                    'storage_token' => json_encode($client->getAccessToken())
                ]);
            }
        }

        return $client;
    }
}
