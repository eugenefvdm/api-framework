<?php

namespace Eugenefvdm\Api;

use Eugenefvdm\Api\Contracts\WhmInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Whm implements WhmInterface
{
    private ?PendingRequest $client = null;

    /**
     * Constructor
     *
     * Credentials are optional at construction time — the singleton is always registered
     * regardless of whether WHM is configured. Actual API calls will throw a RuntimeException
     * if credentials are missing. Use isConfigured() to guard calls in application code.
     *
     * @param  string|null  $username  WHM username (WHM_USERNAME)
     * @param  string|null  $password  WHM password (WHM_PASSWORD)
     * @param  string|null  $server    WHM server URL, e.g. https://server.example.com:2087 (WHM_SERVER)
     */
    public function __construct(
        private readonly ?string $username,
        private readonly ?string $password,
        private readonly ?string $server,
    ) {}

    /**
     * Whether WHM credentials are configured.
     *
     * Use this to guard calls in application code rather than checking config keys directly:
     *
     *   if (Whm::isConfigured()) {
     *       Whm::createEmail(...);
     *   }
     */
    public function isConfigured(): bool
    {
        return (bool) $this->username && (bool) $this->server;
    }

    /**
     * Get the HTTP client instance.
     *
     * @throws \RuntimeException When WHM credentials are not configured.
     */
    private function client(): PendingRequest
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException(
                'WHM is not configured. Set WHM_USERNAME, WHM_PASSWORD, and WHM_SERVER in your .env file.'
            );
        }

        if (! $this->client) {
            $this->client = Http::baseUrl(rtrim($this->server, '/'))
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
     * @link https://api.docs.cpanel.net/openapi/whm/operation/showbw/ WHM API Documentation for showbw
     *
     * @return array Bandwidth information
     */
    public function bandwidth(): array
    {
        return $this->client()->get('/json-api/showbw')->json();
    }

    /**
     * Get cPHulk blacklist records using API version 1
     *
     * @link https://api.docs.cpanel.net/openapi/whm/operation/read_cphulk_records/ WHM API Documentation for read_cphulk_records
     *
     * @return array cPHulk blacklist records
     */
    public function cphulkBlacklist(): array
    {
        return $this->client()->get('/json-api/read_cphulk_records?api.version=1&list_name=black')->json();
    }

    /**
     * Get cPHulk whitelist records using API version 1
     *
     * @link https://api.docs.cpanel.net/openapi/whm/operation/read_cphulk_records/ WHM API Documentation for read_cphulk_records
     *
     * @return array cPHulk whitelist records
     */
    public function cphulkWhitelist(): array
    {
        return $this->client()->get('/json-api/read_cphulk_records?api.version=1&list_name=white')->json();
    }

    /**
     * Create a new email account
     *
     * @link https://api.docs.cpanel.net/openapi/cpanel/operation/add_pop/ WHM API Documentation for add_pop
     *
     * @param  string  $cpanelUsername  The cPanel username that owns the email account
     * @param  string  $email  The email account username (without domain)
     * @param  string  $password  The email account password
     * @return array Response from the API with HTTP status code
     */
    public function createEmail(
        string $cpanelUsername,
        string $email,
        string $password,
        ?string $domain = null,
        ?int $quota = null,
        bool $sendWelcomeEmail = false
    ): array {
        $params = [
            'cpanel_jsonapi_apiversion' => 3,
            'cpanel_jsonapi_user' => $cpanelUsername,
            'cpanel_jsonapi_module' => 'Email',
            'cpanel_jsonapi_func' => 'add_pop',
            'email' => $email,
            'password' => $password,
        ];

        $response = $this->client()->get('/json-api/cpanel', $params)->json();

        // Check for errors
        if (isset($response['result']['errors'])) {
            return [
                'status' => 'error',
                'code' => 400,
                'output' => $response['result']['errors'][0] ?? 'Unknown error occurred',
            ];
        }

        // Success case
        return [
            'status' => 'success',
            'code' => 200,
            'output' => $response['result']['data'] ?? [],
        ];
    }

    /**
     * Delete an email account
     *
     * @link https://api.docs.cpanel.net/openapi/cpanel/operation/delete_pop/ WHM API Documentation for delete_pop
     *
     * @param  string  $cpanelUsername  The cPanel username that owns the email account
     * @param  string  $email  The full email address to delete
     * @return array Response from the API with HTTP status code
     */
    public function deleteEmail(string $cpanelUsername, string $email): array
    {
        $response = $this->client()->get('/json-api/cpanel', [
            'cpanel_jsonapi_apiversion' => 3,
            'cpanel_jsonapi_user' => $cpanelUsername,
            'cpanel_jsonapi_module' => 'Email',
            'cpanel_jsonapi_func' => 'delete_pop',
            'email' => $email,
        ])->json();

        if (isset($response['result']['errors'])) {
            return [
                'status' => 'error',
                'code' => 400,
                'output' => $response['result']['errors'][0] ?? 'Unknown error occurred',
            ];
        }

        return [
            'status' => 'success',
            'code' => 200,
            'output' => $response['result']['data'] ?? [],
        ];
    }

    /**
     * Suspend an email account's login ability
     *
     * @link https://api.docs.cpanel.net/openapi/cpanel/operation/suspend_login/
     *
     * @param  string  $email  The email address to suspend
     * @param  string  $cpanelUsername  The cPanel username that owns the email account
     * @return array Response from the API with HTTP status code
     */
    public function suspendEmail(string $cpanelUsername, string $email): array
    {
        $response = $this->client()->get('/json-api/cpanel', [
            'cpanel_jsonapi_apiversion' => 3,
            'cpanel_jsonapi_user' => $cpanelUsername,
            'cpanel_jsonapi_module' => 'Email',
            'cpanel_jsonapi_func' => 'suspend_login',
            'email' => $email,
        ])->json();

        // Check for email not found error message
        if (isset($response['result']['errors'])) {
            foreach ($response['result']['errors'] as $error) {
                if (str_contains($error, 'You do not have an email account named')) {
                    return [
                        'status' => 'error',
                        'code' => 404,
                        'output' => "Email address '$email' not found",
                    ];
                }
            }
        }

        // Check for other messages (e.g. already suspended)
        if (! empty($response['result']['messages'])) {
            return [
                'status' => 'error',
                'code' => 400,
                'output' => $response['result']['messages'][0],
            ];
        }

        // Success case
        return [
            'status' => 'success',
            'code' => 200,
            'output' => [],
        ];
    }

    /**
     * Unsuspend an email account's login ability
     *
     * @link https://api.docs.cpanel.net/openapi/cpanel/operation/unsuspend_login/
     *
     * @param  string  $email  The email address to unsuspend
     * @param  string  $cpanelUsername  The cPanel username that owns the email account
     * @return array Response from the API with HTTP status code
     */
    public function unsuspendEmail(string $cpanelUsername, string $email): array
    {
        $response = $this->client()->get('/json-api/cpanel', [
            'cpanel_jsonapi_apiversion' => 3,
            'cpanel_jsonapi_user' => $cpanelUsername,
            'cpanel_jsonapi_module' => 'Email',
            'cpanel_jsonapi_func' => 'unsuspend_login',
            'email' => $email,
        ])->json();

        // Check for email not found error message
        if (isset($response['result']['errors'])) {
            foreach ($response['result']['errors'] as $error) {
                if (str_contains($error, 'You do not have an email account named')) {
                    return [
                        'status' => 'error',
                        'code' => 404,
                        'output' => "Email address '$email' not found",
                    ];
                }
            }
        }

        // Check for already unsuspended message
        if (! empty($response['result']['messages'])) {
            return [
                'status' => 'error',
                'code' => 400,
                'output' => $response['result']['messages'][0],
            ];
        }

        // Success case
        return [
            'status' => 'success',
            'code' => 200,
            'output' => [],
        ];
    }

    /**
     * Generate a random password of 12 characters
     */
    public static function generatePassword(): string
    {
        return Str::random(12);
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
