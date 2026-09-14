<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tap Payments
    |--------------------------------------------------------------------------
    |
    | When enabled, new checkouts (service, project, renew order) are paid
    | through Tap instead of MyFatoorah. The secret key authenticates API
    | calls and signs webhooks (hashstring header): keep it server-side only.
    |
    */

    'enabled' => (bool) env('TAP_ENABLED', false),

    'secret_key' => env('TAP_SECRET_KEY'),

    'public_key' => env('TAP_PUBLIC_KEY'),

    'merchant_id' => env('TAP_MERCHANT_ID'),

    'base_url' => env('TAP_BASE_URL', 'https://api.tap.company/v2'),

    // Currency the checkout amounts are expressed in.
    'currency' => env('TAP_CURRENCY', 'SAR'),

    // Public URLs Tap calls back. Default to the "tap.webhook" / "tap.return" routes.
    'webhook_url' => env('TAP_WEBHOOK_URL'),
    'return_url' => env('TAP_RETURN_URL'),

    // Frontend pages the customer lands on after the payment has been verified.
    'success_url' => env('TAP_SUCCESS_URL', env('MYFATOORAH_SUCCESS_URL')),
    'failure_url' => env('TAP_FAILURE_URL', env('MYFATOORAH_FAILURE_URL')),

    'timeout' => (int) env('TAP_TIMEOUT', 20),

];
