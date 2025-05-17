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
10. WHMCS
11. X (Twitter)
12. ZADomains

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

    'whmcs' => [
        'url' => env('WHMCS_URL'),
        'api_identifier' => env('WHMCS_API_IDENTIFIER'),
        'api_secret' => env('WHMCS_API_SECRET'),
        'database_name' => env('WHMCS_DATABASE_NAME'),
        'database_username' => env('WHMCS_DATABASE_USERNAME'),
        'database_password' => env('WHMCS_DATABASE_PASSWORD'),
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

Precede access by the facade namespace, e.g.

```php
use Eugenefvdm\Api\Facades\Bulksms;
Bulksms::sendSms("Hello SMS!", ["27600000000"]);
```

Here is a list of all the API calls:

```php
Bulksms::sendSms("Hello SMS!", ["27600000001","2760000000"]);

$discordUser = Discord::user("123456789012345678");

$nsRecords = Dns::NS("example.com");

$digMxRecords = Dns::MX("example.com"); // Use `dig` to get MX records
$nativeMxRecords = Dns::MX("example.com", false); // Use PHP native to get MX recorss

Fail2ban:setServer("username", "hostname", 22); // Port is optional
$firstEntry = Fail2ban::first("192.168.1.1");
$lastEntry = Fail2ban::last("192.168.1.1");

$unrepliedReviews = Hellopeter::unrepliedReviews();

Slack::sendText("Hello Slack!");

Tail::setServer("username", "hostname", 22);
$logEntry = Tail::last("/var/log/mail.log", "user@example.com", 1); // 1 = optional number of log entries to return

Telegram::sendMessage("Hi Telegram!");

$bandwidth = Whm::bandwidth();
Whm::disableEmail('cPanel_username','user@example.com');
Whm::enableEmail('cPanel_username','user@example.com');
$whitelist = Whm::cphulkWhitelist();
$blacklist = Whm::cphulkBlacklist();
Whm::createEmail('user@example.com');

Whmcs::createClientGroup($name, $colour = '#ffffff');
Whmcs::createCustomClientField($name, $type = 'text');

$userId = X::userId("x_username");
$tweets = X::tweets($userId['data']['id'], 5);
$userWithLimits = X::userWithRateLimits("x_username");

$registrant = Zadomains::registrant("example.co.za");
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
    - Capitalize only the first letter of API's name, e.g., `Bulksms`, `Zadomains`
    - For compound names, also don't capatalize, e.g., `Hellopeter`
    - Getters should not be prepended by "get", e.g. use `message()` instead of `getMessage()`
    - Setters should be prepended by "set", e.g. `setServer()`
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
