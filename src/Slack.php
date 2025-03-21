<?php

namespace Eugenefvdm\Api;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Slack
{
    private string $webhookUrl;

    public function __construct(string $webhookUrl)
    {
        $this->webhookUrl = $webhookUrl;
    }

    /**
     * Send text to Slack using Incoming Webhook
     *
     * @param string $text The text to send to Slack
     * @return bool Returns true if message was sent successfully
     * @throws \Illuminate\Http\Client\RequestException If the request fails
     */
    public function sendText(string $text): bool
    {
        $response = Http::post($this->webhookUrl, [
            'text' => $text
        ]);

        // Slack returns 200 OK with body "ok" for successful webhook calls
        return $response->successful();
    }
} 