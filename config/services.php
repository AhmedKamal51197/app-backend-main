<?php

return [
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    // Website instant refresh after dashboard edits (optional).
    'website' => [
        'revalidate_url' => env('WEBSITE_REVALIDATE_URL'),
        'revalidate_secret' => env('LANDING_REVALIDATE_SECRET'),
    ],
];
