<?php

namespace Eugenefvdm\Api;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class Whm
{
    private ?PendingRequest $client = null;

    private string $username;

    private string $password;

    private string $server;

    /**
     * Constructor
     *
     * @param  string  $username  Whm username
     * @param  string  $password  Whm password
     * @param  string  $server  Whm server URL (e.g. https://server.example.com:2087)
     */
    public function __construct(string $username, string $password, string $server)
    {
        $this->username = $username;
        $this->password = $password;
        $this->server = rtrim($server, '/');
    }

    /**
     * Get the HTTP client instance
     */
    private function client(): PendingRequest
    {
        if (! $this->client) {
            $this->client = Http::baseUrl($this->server)
                ->withHeaders([
                    'Authorization' => 'WHM '.$this->username.':'.$this->password,
                ])
                ->withoutVerifying();
        }

        return $this->client;
    }

    /**
     * Get bandwidth information for all domains
     *
     * @return array Bandwidth information
     */
    public function bandwidth(): array
    {
        return (array)$this->client()->get('/json-api/showbw')->json();
    }

    /**
     * Set the HTTP client (used for testing)
     *
     * @param  PendingRequest  $client  The HTTP client to use
     */
    public function setClient(PendingRequest $client): void
    {
        $this->client = $client;
    }
}
