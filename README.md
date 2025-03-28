# API Framework

[![Tests](https://github.com/eugenefvdm/api-framework/actions/workflows/tests.yml/badge.svg)](https://github.com/eugenefvdm/api-framework/actions/workflows/tests.yml)
[![Larastan](https://github.com/eugenefvdm/api-framework/actions/workflows/larastan.yml/badge.svg)](https://github.com/eugenefvdm/api-framework/actions/workflows/larastan.yml)
[![Downloads](https://img.shields.io/packagist/dt/eugenefvdm/api-framework.svg)](https://packagist.org/packages/eugenefvdm/api-framework)

Another day, another API.

A set of Laravel API service providers.

1. BulkSMS
2. Discord
3. DNS
4. Fail2ban
5. Hello Peter
6. Slack
7. Tail
8. Telegram
9. WHM/cPanel
10. X (Twitter)
11. ZADomains

## Installation

```bash
composer require eugenefvdm/api-framework
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
$bulkSms = Bulksms::sendSms("Hello SMS!", ["27600000000"]);

use Eugenefvdm\Api\Facades\Discord;
$discord = Discord::user("123456789012345678");

use Eugenefvdm\Api\Facades\Dns;
$nameservers = Dns::NS("example.com"); // Use PHP native

use Eugenefvdm\Api\Facades\Dns;
$nameservers = Dns::NS("example.com", true); // Use Dig

use Eugenefvdm\Api\Facades\Fail2ban;
Fail2ban:setServer("username", "hostname", 22); // Port is optional
$firstFail2banEntry = Fail2ban::first("192.168.1.1");
$lastFail2banEntry = Fail2ban::last("192.168.1.1");

use Eugenefvdm\Api\Facades\Hellopeter;
$hellopeterUnrepliedReviews = Hellopeter::unrepliedReviews();

use Eugenefvdm\Api\Facades\Slack;
$textSendResult = Slack::sendText("Hello Slack!");

use Eugenefvdm\Api\Facades\Tail;
Tail::setServer("username", "hostname", 22);
$mailLogs = Tail::last("user@example.com", 1); // 1 = number of entries (optional)

use Eugenefvdm\Api\Facades\Telegram;
$messageSendResult = Telegram::sendMessage("Hi Telegram!");

use Eugenefvdm\Api\Facades\Whm;
$whmBandwidth = Whm::bandwidth();

use Eugenefvdm\Api\Facades\X;
$userId = X::userId("x_username");
$tweets = X::tweets($userId['data']['id'], 5);
$userWithLimits = X::userWithRateLimits("x_username");

use Eugenefvdm\Api\Facades\Zadomains;
$zadomainsRegistrant = Zadomains::registrant("example.co.za");
```

## Testing

```bash
vendor/bin/pest
```

## Design philosophy

APIs can be hard. Reading documentation is a drag. And what if you only want to use a few calls? Do you really have to learn everything? This framework gives you the power of many APIs in one package. It's minimalist and uses Laravel's facades for easy access.
Each call is tested using stubs which doubles as a handy reference.

## Contribution Guidelines

New contributions are super welcome!

1. Fork the repository
2. Create a new branch for your changes (`git checkout -b feature/amazing-api`)
3. Make your changes
4. Run the tests (`./vendor/bin/pest`)
5. Run Larastan (`./vendor/bin/phpstan analyse`)
5. Submit a pull request

### Code Style & Standards

- Pint is installed, so simply run `vendor/bin/pint` to make your code clean
- For new APIs and API calls:
  - Add feature tests with stubs (see existing examples)
  - Ensure stubs are redacted of sensitive information
  - Follow the existing naming conventions:
    - Capitalize only the first letter of API's name (e.g., `BulkSms`, `Zadomains`)
    - For compound names, remove also don't capatalize (e.g., `Hellopeter`)
    - Getters should not be prepended by "get" (e.g., `message()` instead of `getMessage()`)
    - Setter should be prepended by "set", e.g. `setServer()`
  - All API responses should follow this format:
    ```php
    // Success response
    [
        "status" => "success",
        "output" => $output,
    ]

    // Error response
    [
        "status" => "error",
        "output" => "Error message here",
    ]
    ```

### Need Help?

If you have an idea for a new API or API call but don't have time to implement it, feel free to open a new issue to see if we can do an implementation.
