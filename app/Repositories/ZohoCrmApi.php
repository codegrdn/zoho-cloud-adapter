<?php

namespace App\Repositories;

use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\oauth\ZohoOAuth;

class ZohoCrmApi
{
    public static function initialize($user)
    {
        $configuration = [
            'apiBaseUrl' => config('zoho.api_base_url'),
            'apiVersion' => 'v2',
            // 'sandbox'  => true,
            'client_id' => config('zoho.client_id'),
            'client_secret' => config('zoho.secret'),
            'redirect_uri' => config('zoho.redirect_uri'),
            'accounts_url' => config('zoho.account_url'),
            'currentUserEmail' => $user->email,
            'access_type' => 'offline',
            'persistence_handler_class_name' => '\App\Repositories\ZohoPersistence',
            'persistence_handler_class' => base_path('app/Repositories/ZohoPersistenceInterface.php'),
        ];

        ZCRMRestClient::initialize($configuration);
    }

    public static function generateOauth($user, $grantToken) {
        self::initialize($user);

        ZohoOAuth::getClientInstance()->generateAccessToken($grantToken);

        return true;
    }
}
