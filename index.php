<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Eugenevdm\HelloPeter;
use Eugenevdm\BulkSMS;
use Eugenevdm\Slack;
use Eugenevdm\Telegram;
use Eugenevdm\ZADomains;
// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize the HelloPeter client
$client = new HelloPeter($_ENV['HELLO_PETER_API_KEY']);

try {
    // Send SMS
    if ($_ENV['ENABLE_BULKSMS'] === 'true') {
        $sender = new BulkSMS($_ENV['BULKSMS_USERNAME'], $_ENV['BULKSMS_PASSWORD']);
        $recipients = explode(',', $_ENV['BULKSMS_RECIPIENTS']);
        $sender->sendSMS($message, $recipients);
    }

    // Send Slack notification
    if ($_ENV['ENABLE_SLACK'] === 'true') {
        $slack = new Slack($_ENV['SLACK_WEBHOOK_URL']);
        $slack->sendMessage($message);
    }

    // Send Telegram notification
    if ($_ENV['ENABLE_TELEGRAM'] === 'true') {
        $telegram = new Telegram($_ENV['TELEGRAM_BOT_TOKEN'], $_ENV['TELEGRAM_CHAT_ID']);
        $telegram->sendMessage($message);    
    }

    // Send ZADomains notification
    if ($_ENV['ENABLE_ZADOMAINS'] === 'true') {
        $zadomains = new ZADomains($_ENV['ZADOMAINS_USERNAME'], $_ENV['ZADOMAINS_PASSWORD']);
        $result = $zadomains->getDomainInfo($_ENV['ZADOMAINS_TEST_DOMAIN']);
        ray($result);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
