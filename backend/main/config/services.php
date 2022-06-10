<?php

return [
    'happyday' => [
        'client_id' => env('HAPPYDAY_CLIENT_ID'),
        'client_secret' => env('HAPPYDAY_CLIENT_SECRET'),
        'redirect' => env('HAPPYDAY_REDIRECT_URI', env('APP_URL') . '/v1/security/oauth/happyday/callback'),
        'host' => env('HAPPYDAY_HOST'),
        'driver' => env('HAPPYDAY_DRIVER', 'local'),
        'access_token' => env('HAPPYDAY_ACCESS_TOKEN'),
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_KEY'),
        'client_secret' => env('FACEBOOK_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI', env('APP_URL') . '/socialite/facebook/callback'),
        'default' => env('DEFAULT_AVATAR_FACEBOOK', '10354686_10150004552801856_220367501106153455_n.jpg?_nc_cat=1&_nc_oc=AQkOJUiJc3ztT8ywOQkCVczApp4_QgX8G2jxC-dNoapi1NV-YD4iov1OBlJevjBY1g8&_nc_ht=scontent.fhrk1-1.fna&oh=1aa1933dd570f1fb19d9816cc7303c54&oe=5E5F991B'),
        'driver' => env('FACEBOOK_DRIVER', 'socialite')
    ],
    'vkontakte' => [
        'client_id' => env('VKONTAKTE_KEY'),
        'client_secret' => env('VKONTAKTE_SECRET'),
        'redirect' => env('VKONTAKTE_REDIRECT_URI', env('APP_URL') . '/socialite/vkontakte/callback'),
        'default' => env('DEFAULT_AVATAR_VK', 'camera_50.png?ava=1'),
        'driver' => env('VKONTAKTE_DRIVER', 'local'),
        'host' => env('VKONTAKTE_HOST'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/socialite/google/callback'),
        'driver' => env('GOOGLE_DRIVER', 'socialite')
    ]
];
