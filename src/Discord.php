<?php

namespace Eugenefvdm\Api;

use Illuminate\Support\Facades\Http;

class Discord
{
    private $botToken;

    private $baseUrl = 'https://discord.com/api/v10';

    public function __construct(string $botToken)
    {
        $this->botToken = $botToken;
    }

    /**
     * Get user information by user ID
     *
     * @param  string  $userId  The Discord user ID to lookup
     * @return array Returns user data including id, username, and avatar
     */
    public function getUser(string $userId): array
    {
        $url = "{$this->baseUrl}/users/{$userId}";

        $response = Http::withHeaders([
            'Authorization' => "Bot {$this->botToken}",
            'Content-Type' => 'application/json',
        ])->get($url);

        return $response->json();
    }
}
