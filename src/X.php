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

        return (array) $response->json();
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

        return (array) $response->json();
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

        $rateLimitReset = $response->header('x-rate-limit-reset');
        $secondsUntilReset = $rateLimitReset ? max(0, (int) $rateLimitReset - time()) : null;

        return [
            'data' => $response->json(),
            'headers' => [
                'api-version' => $response->header('api-version'),
                'date' => $response->header('date'),
                'x-rate-limit-limit' => $response->header('x-rate-limit-limit'),
                'x-rate-limit-reset' => $rateLimitReset,
                'x-rate-limit-remaining' => $response->header('x-rate-limit-remaining'),
                'x-app-limit-24hour-limit' => $response->header('x-app-limit-24hour-limit'),
                'x-app-limit-24hour-reset' => $response->header('x-app-limit-24hour-reset'),
                'x-app-limit-24hour-remaining' => $response->header('x-app-limit-24hour-remaining'),
                'x-response-time' => $response->header('x-response-time'),
            ],
            'extra' => [
                'rate_limit_remaining_seconds' => $secondsUntilReset,
            ],
        ];
    }
}
