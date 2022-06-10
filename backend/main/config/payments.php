<?php

return [
    'YooMoneyProvider' => [
        'adapter' => env('PAYMENTS_ADAPTER', '\App\Services\Billing\PaymentAdapters\YooMoneyAdapter'),
        'id' => env('PAYMENTS_ADAPTER_ID', '664575'),
        'key' => env('PAYMENTS_ADAPTER_KEY', 'test_24alqygWQYSdA2K2JWd3tYWPjMfjrMmRa23sNUrkmk4'),
        'redirect_url' => config('app.front_url') . '/payment/waiting',
        'default_amount' => env('PAYMENTS_DEFAULT_AMOUNT', 1),
        'default_description' => env('PAYMENTS_DEFAULT_DESCRIPTION', ''),
    ]
];
