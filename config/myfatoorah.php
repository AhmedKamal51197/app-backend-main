<?php

return [
    'token' => env('MYFATOORAH_TOKEN'),
    'endpoints' => [
        'url' => env('MYFATOORAH_API_URL'),
        'checkout' => env('MYFATOORAH_API_SEND_PAYMENT'),
        'initiate_session' => env('MYFATOORAH_INITIATE_SESSION'),
        'payment_status' => env('MYFATOORAH_API_PAYMENT_STATUS'),
        'initiate_payment' => env('MYFATOORAH_API_PAYMENT_METHODS'),
        'webhook' => env('MYFATOORAH_API_WEBHOOK'),
        'success' => env('MYFATOORAH_SUCCESS_URL'),
        'failure' => env('MYFATOORAH_FAILURE_URL'),
    ],
];
