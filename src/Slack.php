<?php

namespace Eugenevdm;

use GuzzleHttp\Exception\GuzzleException;

class Slack
{
    private $webhookUrl;
    private $client;

    public function __construct(string $webhookUrl)
    {
        $this->webhookUrl = $webhookUrl;
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * Send message to Slack
     *
     * @param array $message The message to send to Slack
     * @return array
     * @throws GuzzleException
     */
    public function sendMessage(string $message)
    {
        $json = [
            'text' => "$message"
        ];

        $response = $this->client->post($this->webhookUrl, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => $json
        ]);

        return json_decode($response->getBody(), true);
    }
} 