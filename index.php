<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Eugenevdm\HelloPeter;
use Eugenevdm\BulkSMS;
use Eugenevdm\Discord;
use Eugenevdm\Slack;
use Eugenevdm\Telegram;
use Eugenevdm\ZADomains;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    // Send an SMS using the BulkSMS API
    if ($_ENV['ENABLE_BULKSMS'] === 'true') {
        $sender = new BulkSMS($_ENV['BULKSMS_USERNAME'], $_ENV['BULKSMS_PASSWORD']);
        $recipients = explode(',', $_ENV['BULKSMS_RECIPIENTS']);
        $sender->sendSMS($message, $recipients);
    }

    // Get information about a Discord user
    if ($_ENV['ENABLE_DISCORD'] === 'true') {
        $userId = $_ENV['DISCORD_USER_ID'];
        $discord = new Discord();
        $discord->getUser($userId);
    }

    // Get unreplied reviews from Hello Peter
    if ($_ENV['ENABLE_HELLO_PETER'] === 'true') {
        $client = new HelloPeter($_ENV['HELLO_PETER_API_KEY']);
        $unrepliedReviews = $client->getUnrepliedReviews();
        ray($unrepliedReviews);
    }

    // Send a Slack notification
    if ($_ENV['ENABLE_SLACK'] === 'true') {
        $slack = new Slack($_ENV['SLACK_WEBHOOK_URL']);
        $slack->sendMessage($message);
    }

    // Send a Telegram notification
    if ($_ENV['ENABLE_TELEGRAM'] === 'true') {
        $telegram = new Telegram($_ENV['TELEGRAM_BOT_TOKEN'], $_ENV['TELEGRAM_CHAT_ID']);
        $telegram->sendMessage($message);    
    }

    // Retrieve ZADomains Domain Information
    if ($_ENV['ENABLE_ZADOMAINS'] === 'true') {
        $zadomains = new ZADomains($_ENV['ZADOMAINS_USERNAME'], $_ENV['ZADOMAINS_PASSWORD']);

        // $result = $zadomains->getDomainSelectAllByContact('Eugene van der Merwe');
        // $data = json_decode($result->Domain_SelectAll_ByContactResult, true); 
        // ray($data);

        // $result = $zadomains->getDomainSelectInfo($_ENV['ZADOMAINS_TEST_DOMAIN']);
        // $data = json_decode($result->Domain_Select_InfoResult, true); 
        // ray($data);

        $result = $zadomains->getDomainSelect($_ENV['ZADOMAINS_TEST_DOMAIN']);
        $data = json_decode($result->Domain_SelectResult, true); 
        ray($data['Response_Value']['OwnerEmail']);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
