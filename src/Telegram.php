<?php

namespace Eugenefvdm;

use GuzzleHttp\Exception\GuzzleException;

class Telegram
{
    private $botToken;
    private $chatId;
    private $client;
    private $apiUrl = 'https://api.telegram.org/bot';

    public function __construct(string $botToken, string $chatId)
    {
        $this->botToken = $botToken;
        $this->chatId = $chatId;
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * Send message to Telegram
     *
     * @param string $message The message to send to Telegram
     * @return array
     * @throws GuzzleException
     */
    public function sendMessage(string $message)
    {
        $endpoint = $this->apiUrl . $this->botToken . '/sendMessage';
        
        $json = [
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];

        $response = $this->client->post($endpoint, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => $json
        ]);

        return json_decode($response->getBody(), true);
    }
} 