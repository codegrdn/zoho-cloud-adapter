<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$throttle = env('THROTTLE_NUM');

/** @var \Laravel\Lumen\Routing\Router $router */
$router->get('/', function () use ($router) {
    return 'o hi mark';
});

$router->get('zoho-oauth', [
    'as' => 'zoho-oauth-callback', 'uses' => 'AuthController@zohoOauthCallback'
]);

$router->group(['prefix' => 'onedrive'], function() use ($router) {
    $router->get('auth', ['as' => 'onedrive-auth', 'uses' => 'OneDriveAuthController@auth']);
    $router->get('callback', ['uses' => 'OneDriveAuthController@callback']);
});

$router->get('/auth/google-drive', 'AuthController@authGoogleDrive');
$router->get('/auth/google-drive-callback', 'AuthController@authGoogleDriveCallback');

$router->group(['prefix' => 'api/'], function () use ($router) { //'middleware' => ['throttle:'.(int)$throttle.',1']
    $router->get('/auth/register', 'AuthController@register');
    $router->post('/auth/register', 'AuthController@register');
    // if post register used we will redirect below so the session param will be set and in oauth callback we would identify user
    $router->get('/auth/zoho-auth', [
        'as' => 'zoho-auth', 'uses' => 'AuthController@zohoAuth'
    ]);

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->get('/sync', 'ApiController@sync');
    });

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->get('/sync-google-drive', 'ApiController@syncGoogleDrive');
    });

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->get('sync-onedrive', ['uses' => 'ApiController@syncOneDrive']);
    });
});
