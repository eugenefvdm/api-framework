<?php

return [
    'bulksms' => [
        'username' => env('BULKSMS_USERNAME'),
        'password' => env('BULKSMS_PASSWORD'),
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
    ],

    'zadomains' => [
        'username' => env('ZADOMAINS_USERNAME'),
        'password' => env('ZADOMAINS_PASSWORD'),
    ],
];
