<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

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
