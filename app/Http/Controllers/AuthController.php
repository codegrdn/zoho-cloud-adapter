<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Repositories\ZohoCrmApi;
use App\Repositories\GoogleDriveWrapper;

class AuthController extends BaseController
{
    /**
     * Register user in portal
     * @bodyParam reg_token string required token to match with env val
     * @bodyParam name string required
     * @bodyParam email string required
     * @bodyParam password string required
     * @bodyParam settings json required {"zoho_inventory_key": "", "zoho_inventory_organization_id": "", "zoho_crm_client_id": "", "zoho_crm_secret": "", "zoho_crm_grant_token": "", "zoho_crm_email": "", "zoho_crm_redirect_uri": ""} set both inventory and crm credentials or one of them
     * @bodyParam create_potential boolean
     * @bodyParam create_account boolean
     * @bodyParam create_products boolean
     * @bodyParam send_errors boolean
     * @bodyParam extraFieldsMapping json mapping for create/update order (see this actions doc for json) actions to set values from meta_data or set them as predefined value for Contacts, Deals, Accounts, SalesOrders modules {"Contacts":{"Assistant":"$some constant$","Asst_Phone":"_aftership_tracking_number"}}

     * @response 200 {
     *  "user_id": 4,
     *  "Status": 0
     *  "Msg":"user created",
     * }
     *
     * @response 400 {
     *  "Status":1,
     *  "Msg":"Duplicated user",
     * }
     * @response 400 {
     *  "Status":1,
     *  "Msg":"Missed require fields",
     * }
     * @response 400 {
     *  "Status":1,
     *  "Msg":"Not valid reg_token",
     * }
     * @response 400 {
     *  "Status":1,
     *  "Msg":"not enought api data provided",
     * }
     * @response 400 {
     *  "Status":1,
     *  "Msg":"Zoho errors",
     * }
     * @response 400 {
     *  "Status":1,
     *  "Msg":"Server error , contact support",
     * }
     */
    public function register(Request $request)
    {
        $code = 400;
        $require_params = [];

        foreach (['email','name','password','reg_token', 'platform_type'] as $key){
            if(!$request->exists($key)){
                $missedRequired[] = $key;
            }
        }

        if ( ($request->platform_type === 'dropbox') && (!$request->exists('storage_token')) ) {
            $missedRequired[] = 'storage_token';
        }

        if(!empty($missedRequired)){
            return response()->json([
                'Status'        => 1,
                'Msg'           => "Missed require fields",
                'missed_fields' => $missedRequired,
            ], 400);
        }

        if(env('REG_TOKEN') != $request->reg_token){
            return response()->json([
                'status' => 'error',
                'message' => "Not valid reg_token"
            ], 400);
        }

        $user = User::where('email','=',$request->email)->first();
        $sendErrors = true;
        $redirectUrl = route('zoho-oauth-callback');
        $clientId = env('ZOHO_CLIENT_ID');
        $scopes = 'ZohoCRM.bulk.read,ZohoCRM.modules.all,ZohoSearch.securesearch.ALL,ZohoCRM.users.all,Aaaserver.profile.Read,ZohoCRM.settings.ALL,ZohoCRM.org.ALL';
        $zohoAuthRedirectUrl = "https://accounts.zoho.com/oauth/v2/auth?scope={$scopes}&client_id={$clientId}&response_type=code&access_type=offline&redirect_uri={$redirectUrl}&prompt=consent";

        if ($request->has('send_errors') && is_bool($request->send_errors)) {
            $sendErrors = $request->send_errors;
        }

        if(!empty($user->id)){
            $_SESSION['userId'] = $user->id;

            if ($request->isMethod('get')) {
                return redirect($zohoAuthRedirectUrl);
            } else {

                $response = [
                    'status' => 'success',
                    'message' => 'a new user successfully created',
                    'data' => [
                        'authZoho' => route('zoho-auth', ['hash'=> $user->auth_hash]),
                    ]
                ];

                switch ($request['platform_type']) {
                    case "onedrive":
                        $response['data']['authOneDrive'] = route('onedrive-auth', ['auth_hash' => $user->auth_hash]);
                        break;
                    case "google_drive":
                        $response['data']['authOneDrive'] = route('google-drive-auth', ['auth_hash' => $user->auth_hash]);
                        break;
                }

                return response(json_encode($response, JSON_UNESCAPED_SLASHES));
            }
        } else {
            $settings = [
                'mapping' => !$request->has('mapping') ? [] : json_decode($request->mapping),
                'module' => !$request->has('module') ? [] : $request->module,
                'sendErrors' => $sendErrors,
            ];

            $user_data = [
                'email'         => $request->email,
                'name'          => $request->name,
                'auth_hash'     => md5($request->email . $request->name),
                'password'      => app('hash')->make($request->password),
                'platform_type' => $request->platform_type,
                'storage_token' => $request->storage_token,
                'settings'      => json_encode($settings),
            ];
            $user = User::create($user_data);
            $_SESSION['userId'] = $user->id;

            if ($request->isMethod('get')) {
                return redirect($zohoAuthRedirectUrl);
            } else {

                $response = [
                    'status' => 'success',
                    'message' => 'a new user successfully created',
                    'data' => [
                        'authZoho' => route('zoho-auth', ['hash'=> $user->auth_hash]),
                    ]
                ];

                switch ($request['platform_type']) {
                    case "onedrive":
                        $response['data']['authOneDrive'] = route('onedrive-auth', ['auth_hash' => $user->auth_hash]);
                        break;
                    case "google_drive":
                        $response['data']['authOneDrive'] = route('google-drive-auth', ['auth_hash' => $user->auth_hash]);
                        break;
                }

                return response(json_encode($response, JSON_UNESCAPED_SLASHES));
            }
        }
    }

    public function zohoAuth(Request $request)
    {
        $user = User::where('auth_hash', $request->hash)->first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'user not found']);
        }

        $_SESSION['userId'] = $user->id;
        $redirectUrl = route('zoho-oauth-callback');
        $clientId = env('ZOHO_CLIENT_ID');
        $scopes = 'ZohoCRM.bulk.read,ZohoCRM.modules.all,ZohoSearch.securesearch.ALL,ZohoCRM.users.all,Aaaserver.profile.Read,ZohoCRM.settings.ALL,ZohoCRM.org.ALL';
        $zohoAuthRedirectUrl = "https://accounts.zoho.com/oauth/v2/auth?scope={$scopes}&client_id={$clientId}&response_type=code&access_type=offline&redirect_uri={$redirectUrl}&prompt=consent";

        return redirect($zohoAuthRedirectUrl);
    }

    public function zohoOauthCallback(Request $request)
    {
        $user = User::find($_SESSION['userId']);

        ZohoCrmApi::generateOauth($user, $request->code);

        return response(json_encode([
            'status' => true,
            'message' => 'user created with zoho data',
            'data' => ['userHash' => $user->auth_hash,]
        ], JSON_UNESCAPED_SLASHES));
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Laravel\Lumen\Http\Redirector
     * @throws \Illuminate\Validation\ValidationException
     *
     * @todo implement refresh token
     */
    public function authGoogleDrive(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
        ]);

        $_SESSION['userId'] = $request->user_id;
        $user = User::find($_SESSION['userId']);
        $client = GoogleDriveWrapper::initClient($user);

        if ($client->isAccessTokenExpired()) {
            return redirect($client->createAuthUrl());
        }

        return response()->json([
            'status' => true,
            'message' => "User with id " . $user->id . " already is authorized",
        ]);
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authGoogleDriveCallback(Request $request)
    {
        $this->validate($request, [
            'code' => 'required'
        ]);

        $authCode = $request->code;
        $user = User::find($_SESSION['userId']);
        $client = GoogleDriveWrapper::initClient($user);

        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        DB::table('users')->where('id', $user->id)->update([
            'platform_type' => 'google_drive',
            'storage_token' => json_encode($accessToken)
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Google Drive Access Token is fetched',
            'data' => [
                'userHash' => $user->auth_hash
            ]
        ]);
    }
}
