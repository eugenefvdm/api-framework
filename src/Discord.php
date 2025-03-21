<?php

namespace Eugenefvdm\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class Discord
{
    private $botToken;
    private $client;
    private $baseUrl = 'https://discord.com/api/v10';

    public function __construct(string $botToken, ?ClientInterface $client = null)
    {
        $this->botToken = $botToken;
        $this->client = $client ?? new Client();
    }

    /**
     * Set the HTTP client (used for testing)
     *
     * @param ClientInterface $client The HTTP client to use
     * @return void
     */
    public function setClient(ClientInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * Get user information by user ID
     *
     * @param string $userId The Discord user ID to lookup
     * @return array Returns user data including id, username, and avatar
     * @throws GuzzleException
     */
    public function getUser(string $userId): array
    {
        $url = "{$this->baseUrl}/users/{$userId}";

        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Authorization' => "Bot {$this->botToken}",
                'Content-Type' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}
