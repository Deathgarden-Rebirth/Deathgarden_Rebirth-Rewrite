<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'steam' => [
        'baseUrl'=> env('STEAM_API_URL'),
        'apiKey' => env('STEAM_API_KEY'),
        'appID' => 555440,

        // Socialite Settings
        'client_id' => null,
        'client_secret' => env('STEAM_API_KEY'),
        'redirect' => '/auth/callback',
        'redirect_launcher' => '/auth/launcherCallback',
        'allowed_hosts' => [
            parse_url(env('APP_URL', null), PHP_URL_HOST),
        ],
    ],
];
