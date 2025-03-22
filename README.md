# API Collection

[![Tests](https://github.com/eugenefvdm/api-collection/actions/workflows/tests.yml/badge.svg)](https://github.com/eugenefvdm/api-collection/actions/workflows/tests.yml)
[![Downloads](https://img.shields.io/packagist/dt/eugenefvdm/api-collection.svg)](https://packagist.org/packages/eugenefvdm/api-collection)

Another day, another API.

A set of Laravel API service providers.

- BulkSMS
- Discord
- Hello Peter
- Slack
- Telegram
- ZADomains

## Installation

```bash
composer require eugenefvdm/api-collection
```

## Publish the configuration file

```bash
php artisan vendor:publish --provider="Eugenefvdm\Api\ApiServiceProvider" --tag="config"
```

## Contents of `config/api.php`

```env
return [
    'bulk_sms' => [
        'username' => env('BULK_SMS_USERNAME'),
        'password' => env('BULK_SMS_PASSWORD'),
    ],

    'discord' => [
        'bot_token' => env('DISCORD_BOT_TOKEN'),
    ],

    'hello_peter' => [
        'api_key' => env('HELLO_PETER_API_KEY'),
    ],

    'slack' => [
        'webhook_url' => env('SLACK_WEBHOOK_URL'),
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
    ],

    'za_domains' => [
        'username' => env('ZA_DOMAINS_USERNAME'),
        'password' => env('ZA_DOMAINS_PASSWORD'),
    ],
]; 
```

## Usage

```php
$bulkSmsResult = \Eugenefvdm\Api\Facades\BulkSMS::sendSMS("Hello SMS!", [
  "27600000000"
]);

$discordUser = \Eugenefvdm\Api\Facades\Discord::getUser("123456789012345678");

$helloPeterUnrepliedReviews = \Eugenefvdm\Api\Facades\HelloPeter::getUnrepliedReviews();

$slackTextBoolReturn = \Eugenefvdm\Api\Facades\Slack::sendText(
  "Hello world testing from API collection!"
);

$telegramSendMessageResult = \Eugenefvdm\Api\Facades\Telegram::sendMessage(
  "Hi Telegram!"
);

$zadomainsRegistrant = \Eugenefvdm\Api\Facades\ZADomains::registrant(
  "example.co.za"
);
```

## Testing

```bash
vendor/bin/pest
```

## Design philosophy

APIs can be hard. Reading documentation is a drag. And what if you only want to use a few calls? Do you really have to learn everything? This library collection gives you the power of many APIs in one package. It's minimalist and uses Laravel's facades for easy access.
Each call is tested using stubs which doubles as a handy reference.

## Contribution Guidelines

- Make a pull request
- For each new API and API call, add a feature test with a stub. See the existing examples. Be thorough and make sure the stubs are redacted.
- If you're too busy to add a new API, or a new API call, contact me and I'll oblige where I can: eugene (at) vander.host or +27 82 309-6710.
