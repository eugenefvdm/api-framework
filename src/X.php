<?php

namespace Eugenefvdm\Api;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class X
{
    private string $bearerToken;

    private ?string $consumerKey;

    private ?string $consumerSecret;

    private ?string $accessToken;

    private ?string $accessTokenSecret;

    private string $baseUrl = 'https://api.twitter.com/2';

    private ?PendingRequest $client = null;

    /**
     * Read operations use the app-only bearer token. Posting (write operations)
     * requires OAuth 1.0a user-context credentials, so those four values are
     * only needed when calling tweet().
     */
    public function __construct(
        string $bearerToken,
        ?string $consumerKey = null,
        ?string $consumerSecret = null,
        ?string $accessToken = null,
        ?string $accessTokenSecret = null,
    ) {
        $this->bearerToken = $bearerToken;
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->accessToken = $accessToken;
        $this->accessTokenSecret = $accessTokenSecret;
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

    /**
     * Post a tweet on behalf of the authenticated user
     *
     * Posting is a write operation that the app-only bearer token cannot
     * perform, so this signs the request with OAuth 1.0a user-context
     * credentials instead.
     *
     * @param  string  $text  The tweet text (max 280 characters on standard access)
     * @return array{status: string, output: mixed}
     */
    public function tweet(string $text): array
    {
        if (! $this->canPost()) {
            return [
                'status' => 'error',
                'output' => 'Posting requires OAuth 1.0a credentials: consumer key, consumer secret, access token, and access token secret.',
            ];
        }

        $url = "{$this->baseUrl}/tweets";

        $response = Http::withHeaders([
            'Authorization' => $this->oauth1Header('POST', $url),
            'Content-Type' => 'application/json',
        ])->post($url, ['text' => $text]);

        return $response->successful()
            ? ['status' => 'success', 'output' => $response->json()]
            : ['status' => 'error', 'output' => $response->json() ?? $response->body()];
    }

    /**
     * Whether the OAuth 1.0a user-context credentials needed for posting are set
     */
    public function canPost(): bool
    {
        return $this->consumerKey !== null
            && $this->consumerSecret !== null
            && $this->accessToken !== null
            && $this->accessTokenSecret !== null;
    }

    /**
     * Build an OAuth 1.0a "Authorization" header for a user-context request.
     *
     * A JSON request body is not part of the OAuth signature base string, so
     * only the oauth_* parameters (and any query parameters) are signed.
     *
     * @param  string  $method  HTTP method, e.g. POST
     * @param  string  $url  The full request URL without query parameters
     */
    private function oauth1Header(string $method, string $url): string
    {
        $params = [
            'oauth_consumer_key' => (string) $this->consumerKey,
            'oauth_nonce' => bin2hex(random_bytes(16)),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => (string) time(),
            'oauth_token' => (string) $this->accessToken,
            'oauth_version' => '1.0',
        ];

        ksort($params);

        $paramString = collect($params)
            ->map(fn ($value, $key) => rawurlencode($key).'='.rawurlencode($value))
            ->implode('&');

        $baseString = strtoupper($method)
            .'&'.rawurlencode($url)
            .'&'.rawurlencode($paramString);

        $signingKey = rawurlencode((string) $this->consumerSecret)
            .'&'.rawurlencode((string) $this->accessTokenSecret);

        $params['oauth_signature'] = base64_encode(
            hash_hmac('sha1', $baseString, $signingKey, true)
        );

        ksort($params);

        $header = collect($params)
            ->map(fn ($value, $key) => rawurlencode($key).'="'.rawurlencode($value).'"')
            ->implode(', ');

        return "OAuth {$header}";
    }
}
