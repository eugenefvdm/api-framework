<?php

namespace Eugenefvdm\Api;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class X
{
    private string $bearerToken;
    private string $baseUrl = 'https://api.twitter.com/2';
    private ?PendingRequest $client = null;

    public function __construct(string $bearerToken)
    {
        $this->bearerToken = $bearerToken;
    }

    /**
     * Get the HTTP client instance
     */
    private function client(): PendingRequest
    {
        if (! $this->client) {
            $this->client = Http::baseUrl($this->baseUrl)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->bearerToken}",
                    'Content-Type' => 'application/json',
                ]);
        }

        return $this->client;
    }

    /**
     * Set a custom HTTP client (used for testing)
     */
    public function setClient(PendingRequest $client): void
    {
        $this->client = $client;
    }

    /**
     * Get user ID by username
     *
     * @param  string  $username  The username to lookup
     * @return array User data including id, name, and username
     */
    public function userId(string $username): array
    {
        $response = $this->client()->get("/users/by/username/{$username}");
        return $response->json();
    }

    /**
     * Get tweets for a user
     *
     * @param  string  $userId  The user ID to get tweets for
     * @param  int  $maxResults  Maximum number of tweets to retrieve (default: 5)
     * @return array Tweets data including tweets and metadata
     */
    public function tweets(string $userId, int $maxResults = 5): array
    {
        $response = $this->client()->get("/users/{$userId}/tweets", [
            'max_results' => $maxResults,
        ]);
        return $response->json();
    }

    /**
     * Get user data and rate limits
     *
     * @param  string  $username  The username to lookup
     * @return array User data and rate limit information
     */
    public function userWithRateLimits(string $username): array
    {
        $response = $this->client()->withHeaders([
            'Accept' => 'application/json',
        ])->get("/users/by/username/{$username}");

        return [
            'data' => $response->json(),
            'rate_limits' => [
                'limit' => $response->header('x-rate-limit-limit'),
                'remaining' => $response->header('x-rate-limit-remaining'),
                'reset' => $response->header('x-rate-limit-reset'),
            ],
        ];
    }
} 