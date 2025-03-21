<?php

namespace Eugenefvdm;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Discord
{
    /**
     * Get user information by user ID
     *
     * @param string $userId The Discord user ID to lookup
     * @return array Returns user data including id, username, and avatar
     * @throws GuzzleException
     */
    public static function getUser(string $userId)
    {
        header('Content-Type: application/json');

        $botToken = $_ENV['DISCORD_BOT_TOKEN'];

        $url = "https://discord.com/api/v10/users/{$userId}";

        $client = new Client();

        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => "Bot {$botToken}",
                'Content-Type' => 'application/json',
            ],
        ]);

        echo $response->getBody();
    }
}
