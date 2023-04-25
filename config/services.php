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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'discord' => [
        'client_id' => env('DISCORD_CLIENT_ID'),
        'client_secret' => env('DISCORD_CLIENT_SECRET'),
        'endpoint' => env('DISCORD_ENDPOINT', 'https://discord.com/api'),
        'redirect' => env('DISCORD_REDIRECT_URL'),

        // optional
        'allow_gif_avatars' => (bool)env('DISCORD_AVATAR_GIF', true),
        'avatar_default_extension' => env('DISCORD_EXTENSION_DEFAULT', 'jpg'), // only pick from jpg, png, webp
    ],

    'discord_bot' => [
        'token' => env('DISCORD_BOT_TOKEN'),
        'endpoint' => env('DISCORD_BOT_ENDPOINT'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'endpoint' => env('TWITTER_ENDPOINT', 'https://api.twitter.com'),
        'redirect' => env('TWITTER_REDIRECT_URL'),
    ],

    'telegram' => [
        'bot' => env('TELEGRAM_BOT_ID'),
        'origin' => env('TELEGRAM_ORIGIN'),
        'client_id' => null,
        'client_secret' => env('TELEGRAM_TOKEN'),
        'redirect' => env('TELEGRAM_REDIRECT'),
        'endpoint' => env('TELEGRAM_ENDPOINT', 'https://api.telegram.org/bot'),
    ],

    'coin_market_cap' => [
        'endpoint' =>  env('COIN_MARKET_CAP_ENDPOINT', 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest'),
        'token' =>  env('COIN_MARKET_CAP_TOKEN', ''),
    ]
];
