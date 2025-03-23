# API Collection

[![Tests](https://github.com/eugenefvdm/api-collection/actions/workflows/tests.yml/badge.svg)](https://github.com/eugenefvdm/api-collection/actions/workflows/tests.yml)
[![Downloads](https://img.shields.io/packagist/dt/eugenefvdm/api-collection.svg)](https://packagist.org/packages/eugenefvdm/api-collection)

Another day, another API.

A set of Laravel API service providers.

1. BulkSMS
2. Discord
3. Hello Peter
4. Slack
5. Telegram
6. WHM
7. X (Twitter)
8. ZADomains

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

    'whm' => [
        'username' => env('WHM_USERNAME'),
        'password' => env('WHM_PASSWORD'),
        'server' => env('WHM_SERVER', 'https://server.example.com:2087'),
    ],

    'x' => [
        'bearer_token' => env('X_BEARER_TOKEN'),
    ],

    'za_domains' => [
        'username' => env('ZA_DOMAINS_USERNAME'),
        'password' => env('ZA_DOMAINS_PASSWORD'),
    ],
]; 
```

## Usage

```php
use Eugenefvdm\Api\Facades\Bulksms;
$bulkSMS = Bulksms::sendSMS("Hello SMS!", ["27600000000"]);

use Eugenefvdm\Api\Facades\Discord;
$discord = Discord::user("123456789012345678");

use Eugenefvdm\Api\Facades\Hellopeter;
$hellopeterUnrepliedReviews = Hellopeter::unrepliedReviews();

use Eugenefvdm\Api\Facades\Slack;
$textSendResult = Slack::sendText("Hello Slack!");

use Eugenefvdm\Api\Facades\Telegram;
$messageSendResult = Telegram::sendMessage("Hi Telegram!");

use Eugenefvdm\Api\Facades\Whm;
$whmBandwidth = Whm::bandwidth();

use Eugenefvdm\Api\Facades\X;
$userId = X::userId("eugenefvdm");
$tweets = X::tweets($userId['data']['id'], 5);
$userWithLimits = X::userWithRateLimits("eugenefvdm");

use Eugenefvdm\Api\Facades\Zadomains;
$zadomainsRegistrant = Zadomains::registrant("example.co.za");
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
- Many APIs are brand names. Example, BulkSMS, ZADomains, etc. To avoid confusion with casing, just capatilize the first letter. Even if it's compound names, like Hello[ ]Peter, use `Hellopeter` for the name of the API.
- Getters are not prepended by "get", e.g. getMessage() will be messge().
- If you're too busy to add a new API, or a new API call, contact me and I'll oblige where I can: eugene (at) vander.host or +27 82 309-6710.
