<?php

/** @var Router $router */

use Laravel\Lumen\Routing\Router;

$router->group([
    'middleware' => [],
    'prefix' => 'security'
], function () use ($router) {
    $router->post('registration', 'AuthController@registration');
    $router->post('verify', 'AuthController@verify');
    $router->post('send-code/{user_id}', 'AuthController@sendCode');

    $router->group([
        'prefix' => 'oauth'
    ], function () use ($router) {
        $router->get('{provider}/auth', 'OAuthController@auth');
        $router->get('{provider}/callback', 'OAuthController@callback');
        $router->post('{provider}/auth-by-login', 'OAuthController@authByLogin');
        $router->post('{provider}/change', 'OAuthController@registrationByUid');
    });

    $router->post('preregistration', 'AuthController@preregistration');
});

$router->group([
    'middleware' => [
        'verify',
    ],
    'prefix' => 'security'
], function () use ($router) {
    $router->post('login', 'AuthController@login');
    $router->post('password/reset', 'AuthController@resetPassword');
    $router->post('password/update', 'AuthController@updatePassword');
    $router->post('send-sms-code', 'AuthController@sendSmsCode');
    $router->post('check-sms-code', 'AuthController@checksmsCode');
});

$router->group([
    'middleware' => [
        'auth',
        'verify',
        'member'
    ],
    'prefix' => 'security'
], function () use ($router) {
    $router->post('registration/contacts', 'AuthController@createRegistrationContacts');

    $router->post('logout', 'AuthController@logout');
    $router->post('refresh', 'AuthController@refresh');
});

$router->group([
    'middleware' => [
        //'auth',
        //'verify',
        //'member'
    ],
    'prefix' => 'cabinet',
], function () use ($router) {
    $router->group(['namespace' => 'Cabinet'], function () use ($router) {
        $router->get('objects/list', 'ObjectsController@getList');
        $router->get('objects/counts', 'ObjectsController@getCounts');
        $router->get('objects/{id}', 'ObjectsController@getDetail');
    });
    $router->group(['namespace' => 'Billing'], function () use ($router) {

        $router->group(['prefix' => 'billing'], function () use ($router) {

        });
    });

    $router->post('images/cover/crop/{id}', 'ImagesController@resizeImagesByMidpoint');
});

$router->group(['namespace' => 'Billing'], function () use ($router) {
    $router->post('update-payment', 'PaymentNotificationsController@updatePayment');
});


$router->group([
    'middleware' => [],
    'prefix' => ''
], function () use ($router) {
    $router->get('gallery/{id}/images', 'WebController@getGalleryImages');
    $router->get('gallery/{pid}/{gid}', 'WebController@getGalleryInfo');
    $router->get('gallery/show/{pid}/{gid}', 'WebController@showGallery');
    $router->get('gallery/main', 'WebController@getMainGallery');
    $router->post('gallery/access/show/{pid}/{gid}', 'WebController@getGalleryShowAccess');
    $router->get('gallery/download/{pid}/{gid}', 'WebController@downloadGallery');
    $router->post('gallery/access/download/{pid}/{gid}', 'WebController@getGalleryDownloadAccess');
    $router->get('contact/types', 'WebController@getContactsTypes');
    $router->get('tariffs', 'WebController@getTariffs');
});


$router->group([
    'middleware' => [],
    'prefix' => 'search'
], function () use ($router){
    $router->get('list', 'SearchController@getList');
    $router->get('map', 'SearchController@getMap');
    $router->get('info/{id}', 'SearchController@getInfo');
});

$router->group([
    'middleware' => [],
    'prefix' => 'geo'
], function () use ($router){
    $router->get('cities', 'GeoController@getCities');
});

$router->group([
    'middleware' => [],
    'prefix' => 'data'
], function () use ($router){

    $router->get('/objects/params', 'DataController@getParams');
});
