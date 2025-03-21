# API Collection

Another day, another API.

A set of Laravel API service providers with Facade access to get me through my day.

List of APIs that are called:

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

I generally used Tinkerwell but TweakPHP is also cool.

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

APIs can be hard. Reading documentation is a drag. What if you only want to use one or two calls? Do you really have to learn everything? Okay, so AI makes it easier. But it's still
a lot of working, and testing can be ardeous.

This library only calls `the basics`. Of course, the basics differs for everyone, but if you don't have too much time in the day and you just need one or two calls from one or two APIs, then we're on the same page. If you just need `a few` API calls from `many` APIs, we're on the same book.

## Contribution Guideline

- Make a pull request

- For each new API and API call, ensure an associated feature test with a stub is made. See the existing examples. Be thorough and make sure your stubs are redacted.

- If you're too busy to add a new API, or a new API call, contact me and I'll happily oblige where I can: eugene (at) vander.host or +27 82 309-6710
