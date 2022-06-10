<?php

return [
    'default' => env('SMS_ADAPTER', 'smsru'),
    'smsru' => [
        'class' => '\App\Services\Sms\Adapters\SmsRuAdapter',
        'login' => env('SMS_LOGIN', null),
        'password' => env('SMS_PASSWORD', null),
        'api_key' => env('SMS_API_KEY', null),
        'api_url' => env('SMS_API_URL', 'https://sms.ru/sms/send'),
        'sender' => env('SMS_SENDER', null),
        'phone_from' => env('SMS_PHONE_FROM', null),
    ],
    'fake' => [
        'class' => '\App\Services\Sms\Adapters\FakeAdapter',
        'login' => null,
        'password' => null,
        'api_key' => null,
        'api_url' => null,
        'sender' => null,
        'phone_from' => null,
    ],
];
