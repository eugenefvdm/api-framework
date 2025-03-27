<?php

namespace Eugenefvdm\Api;

use Illuminate\Support\Facades\Http;

class Discord
{
    private string $baseUrl = 'https://discord.com/api/v10';

    public function __construct(private string $botToken)
    {
        $this->botToken = $botToken;
    }

    /**
     * Get user information by user ID
     *
     * @param  string  $userId  The Discord user ID to lookup
     * @return array Returns user data including id, username, and avatar
     */
    public function user(string $userId): array
    {
        $url = "{$this->baseUrl}/users/{$userId}";

        $response = Http::withHeaders([
            'Authorization' => "Bot {$this->botToken}",
            'Content-Type' => 'application/json',
        ])->get($url);

        return $response->json();
    }
}
