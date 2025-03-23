<?php

namespace Eugenefvdm\Api;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class Hellopeter
{
    private string $apiKey;

    private string $baseUrl = 'https://api.hellopeter.com/v5/api/';

    private ?PendingRequest $client = null;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Get the HTTP client instance
     */
    private function client(): PendingRequest
    {
        if (! $this->client) {
            $this->client = Http::baseUrl($this->baseUrl)
                ->acceptJson()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'apiKey' => $this->apiKey,
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
     * Get unreplied reviews
     *
     * @param  array  $parameters  Optional parameters for filtering reviews
     */
    public function unrepliedReviews(array $parameters = []): array
    {
        $response = $this->client()->get('reviews', [
            'status' => 'unreplied,unreplied_comment',
            'channel' => 'HELLOPETER',
        ]);

        return $response->json();
    }
}
