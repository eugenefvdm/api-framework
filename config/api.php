<?php

return [
    'cpanel' => [
        'username' => env('CPANEL_USERNAME'),
        'password' => env('CPANEL_PASSWORD'),
        'server' => env('CPANEL_SERVER'),
    ],

    'bulksms' => [
        'username' => env('BULKSMS_USERNAME'),
        'password' => env('BULKSMS_PASSWORD'),
        'encoding' => env('BULKSMS_ENCODING', '7bit'),
    ],

    'discord' => [
        'bot_token' => env('DISCORD_BOT_TOKEN'),
    ],

    'hellopeter' => [
        'api_key' => env('HELLOPETER_API_KEY'),
    ],

    'slack' => [
        'webhook_url' => env('SLACK_WEBHOOK_URL'),
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
    ],

    'whm' => [
        'username' => env('WHM_USERNAME'),
        'password' => env('WHM_PASSWORD'),
        'server' => env('WHM_SERVER'),
    ],

    // Use either the API or the database credentials or both
    'whmcs' => [
        'url' => env('WHMCS_URL'),
        'api_identifier' => env('WHMCS_API_IDENTIFIER'),
        'api_secret' => env('WHMCS_API_SECRET'),
        'limitnum' => env('WHMCS_LIMITNUM'),
    ],

    'x' => [
        'bearer_token' => env('X_BEARER_TOKEN'),
        // OAuth 1.0a user-context credentials, only required for posting tweets
        'consumer_key' => env('X_CONSUMER_KEY'),
        'consumer_secret' => env('X_CONSUMER_SECRET'),
        'access_token' => env('X_ACCESS_TOKEN'),
        'access_token_secret' => env('X_ACCESS_TOKEN_SECRET'),
    ],

    'zadomains' => [
        'username' => env('ZADOMAINS_USERNAME'),
        'password' => env('ZADOMAINS_PASSWORD'),
    ],
];
