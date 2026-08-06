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
     * @param  string|null  $server  WHM server URL, e.g. https://server.example.com:2087 (WHM_SERVER)
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
        return $this->username !== null && $this->username !== ''
            && $this->password !== null && $this->password !== ''
            && $this->server !== null && $this->server !== '';
    }

    /**
     * Get the HTTP client instance.
     *
     * @throws \RuntimeException When WHM credentials are not configured.
     */
    private function client(): PendingRequest
    {
        $credentials = $this->credentials();

        if (! $this->client) {
            $this->client = Http::baseUrl(rtrim($credentials['server'], '/'))
                ->withHeaders([
                    'Authorization' => 'WHM '.$credentials['username'].':'.$credentials['password'],
                ])
                ->withoutVerifying();
        }

        return $this->client;
    }

    /**
     * @return array{username: string, password: string, server: string}
     */
    private function credentials(): array
    {
        $username = $this->username;
        $password = $this->password;
        $server = $this->server;

        if ($username === null || $username === ''
            || $password === null || $password === ''
            || $server === null || $server === '') {
            throw new \RuntimeException(
                'WHM is not configured. Set WHM_USERNAME, WHM_PASSWORD, and WHM_SERVER in your .env file.'
            );
        }

        return [
            'username' => $username,
            'password' => $password,
            'server' => $server,
        ];
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
     * List hosting plans (packages) available to the authenticated WHM user.
     *
     * @link https://api.docs.cpanel.net/openapi/whm/operation/listpkgs/
     *
     * @param  string  $want  Package scope: all, creatable, editable, or viewable
     * @return array{status: string, code: int, output: list<array<string, mixed>>, reason?: string}
     */
    public function listPackages(string $want = 'all'): array
    {
        $response = $this->client()->get('/json-api/listpkgs', [
            'api.version' => 1,
            'want' => $want,
        ])->json();

        $result = (int) ($response['metadata']['result'] ?? 0);
        $reason = (string) ($response['metadata']['reason'] ?? '');

        if ($result !== 1) {
            return [
                'status' => 'error',
                'code' => 400,
                'output' => [],
                'reason' => $reason !== '' ? $reason : 'Unable to list packages.',
            ];
        }

        $packages = $response['data']['pkg'] ?? [];

        if (! is_array($packages)) {
            $packages = [];
        }

        return [
            'status' => 'success',
            'code' => 200,
            'output' => array_values($packages),
            'reason' => $reason !== '' ? $reason : 'OK',
        ];
    }

    /**
     * Park (alias) a domain onto an existing web virtual host.
     *
     * This is the API behind WHM → DNS Functions → Park a Domain. The UI shows
     * targets as "park.vander.host (park)"; those map to web_vhost_domain and username.
     *
     * @link https://api.docs.cpanel.net/openapi/whm/operation/create_parked_domain_for_user/
     *
     * @return array{status: string, code: int, output: mixed, reason?: string}
     */
    public function parkDomain(string $domain, string $username, string $webVhostDomain): array
    {
        $response = $this->client()->get('/json-api/create_parked_domain_for_user', [
            'api.version' => 1,
            'domain' => $domain,
            'username' => $username,
            'web_vhost_domain' => $webVhostDomain,
        ])->json();

        $result = (int) ($response['metadata']['result'] ?? 0);
        $reason = (string) ($response['metadata']['reason'] ?? '');

        if ($result !== 1) {
            return [
                'status' => 'error',
                'code' => 400,
                'output' => $reason !== '' ? $reason : 'Unable to park domain.',
                'reason' => $reason,
            ];
        }

        return [
            'status' => 'success',
            'code' => 200,
            'output' => $response['data'] ?? [],
            'reason' => $reason !== '' ? $reason : 'OK',
        ];
    }

    /**
     * Return the cPanel username that owns a domain, or null if not found.
     *
     * @link https://api.docs.cpanel.net/openapi/whm/operation/getdomainowner/
     */
    public function domainOwner(string $domain): ?string
    {
        $response = $this->client()->get('/json-api/getdomainowner', [
            'api.version' => 1,
            'domain' => $domain,
        ])->json();

        $user = $response['data']['user'] ?? null;

        if (! is_string($user) || trim($user) === '') {
            return null;
        }

        return trim($user);
    }

    /**
     * List parked domains (aliases) for a cPanel account.
     *
     * @link https://api.docs.cpanel.net/cpanel-api-2/cpanel-api-2-modules-park/cpanel-api-2-functions-park-listparkeddomains
     *
     * @return array{status: string, code: int, output: list<array<string, mixed>>}
     */
    public function listParkedDomains(string $cpanelUsername, ?string $regex = null): array
    {
        $params = [
            'cpanel_jsonapi_apiversion' => 2,
            'cpanel_jsonapi_user' => $cpanelUsername,
            'cpanel_jsonapi_module' => 'Park',
            'cpanel_jsonapi_func' => 'listparkeddomains',
        ];

        if ($regex !== null && $regex !== '') {
            $params['regex'] = $regex;
        }

        $response = $this->client()->get('/json-api/cpanel', $params)->json();

        if (isset($response['cpanelresult']['error'])) {
            return [
                'status' => 'error',
                'code' => 400,
                'output' => [],
            ];
        }

        $data = $response['cpanelresult']['data'] ?? [];

        if (! is_array($data)) {
            $data = [];
        }

        return [
            'status' => 'success',
            'code' => 200,
            'output' => array_values($data),
        ];
    }

    /**
     * Search for a parked domain on the server.
     *
     * Resolves the owning account via getdomainowner (or the optional username),
     * then lists parked domains filtered by the domain name.
     *
     * @return array{domain: string, username: string, status?: string, dir?: string, reldir?: string}|null
     */
    public function findParkedDomain(string $domain, ?string $cpanelUsername = null): ?array
    {
        $username = $cpanelUsername;

        if ($username === null || $username === '') {
            $username = $this->domainOwner($domain);
        }

        if ($username === null || $username === '') {
            return null;
        }

        $listed = $this->listParkedDomains($username, preg_quote($domain, '/'));

        if ($listed['status'] !== 'success') {
            return null;
        }

        foreach ($listed['output'] as $row) {
            $parked = (string) ($row['domain'] ?? '');

            if (strcasecmp($parked, $domain) !== 0) {
                continue;
            }

            $match = [
                'domain' => $parked,
                'username' => $username,
            ];

            if (isset($row['status'])) {
                $match['status'] = (string) $row['status'];
            }

            if (isset($row['dir'])) {
                $match['dir'] = (string) $row['dir'];
            }

            if (isset($row['reldir'])) {
                $match['reldir'] = (string) $row['reldir'];
            }

            return $match;
        }

        return null;
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
