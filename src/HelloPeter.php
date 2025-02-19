<?php

namespace Eugenevdm;

use GuzzleHttp\Exception\GuzzleException;

class HelloPeter
{
    private $apiKey;
    private $client;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.hellopeter.com/v5/api/',
            'headers' => [
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/json',
                'apiKey' => $this->apiKey
            ]
        ]);
    }

    /**
     * Get reviews with optional parameters
     *
     * @param array $parameters Optional parameters for filtering reviews
     * @return array
     * @throws GuzzleException
     */
    public function getUnrepliedReviews(array $parameters = [])
    {
        $response = $this->client->get('reviews', [
            'query' => [
                'status' => 'unreplied,unreplied_comment',
                'channel' => 'HELLOPETER'
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
} 