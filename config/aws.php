<?php

return [
    'default_region' => env('AWS_DEFAULT_REGION'),
    'access_key_id' => env('AWS_ACCESS_KEY_ID'),
    'secret_access_key' => env('AWS_SECRET_ACCESS_KEY'),
    'pinpoint' => [
        'region' => env('AWS_PINPOINT_REGION'),
        'version' => env('AWS_PINPOINT_VERSION', 'latest'),
        'key' => env('AWS_PINPOINT_ACCESS_KEY_ID'),
        'secret' => env('AWS_PINPOINT_SECRET_ACCESS_KEY'),
        'originate' => env('AWS_PINPOINT_ORIGINATE'),
        'app_id' => env('AWS_PINPOINT_APP_ID')
    ],
    'sns' => [
        'key' => env('AWS_SNS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SNS_SECRET_ACCESS_KEY'),
    ]
];
