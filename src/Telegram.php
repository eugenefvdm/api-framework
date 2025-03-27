<?php

namespace Eugenefvdm\Api;

use Illuminate\Support\Facades\Http;

class Telegram
{
    private string $botToken;

    private string $chatId;

    private string $apiUrl = 'https://api.telegram.org/bot';

    public function __construct(string $botToken, string $chatId)
    {
        $this->botToken = $botToken;
        $this->chatId = $chatId;
    }

    /**
     * Send message to Telegram
     *
     * @param  string  $message  The message to send to Telegram
     */
    public function sendMessage(string $message): array
    {
        $endpoint = $this->apiUrl.$this->botToken.'/sendMessage';

        $response = Http::asJson()
            ->post($endpoint, [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

        return (array)$response->json();
    }
}
