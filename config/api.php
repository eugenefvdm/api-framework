return [
    'bulksms' => [
        'username' => env('BULKSMS_USERNAME'),
        'password' => env('BULKSMS_PASSWORD'),
        'recipients' => env('BULKSMS_RECIPIENTS'),
    ],

    'discord' => [
        'webhook_url' => env('DISCORD_WEBHOOK_URL'),
    ],

    'hellopeter' => [
        'api_key' => env('HELLO_PETER_API_KEY'),
    ],

    'slack' => [
        'webhook_url' => env('SLACK_WEBHOOK_URL'),
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
    ],

    'zadomains' => [
        'username' => env('ZADOMAINS_USERNAME'),
        'password' => env('ZADOMAINS_PASSWORD'),
    ],
]; 