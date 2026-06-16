<?php

namespace Eugenefvdm\Api;

use Eugenefvdm\Api\Contracts\CpanelInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class Cpanel implements CpanelInterface
{
    private ?PendingRequest $client = null;

    /**
     * Constructor
     *
     * Credentials are optional at construction time — the singleton is always registered
     * regardless of whether cPanel is configured. Actual API calls will throw a RuntimeException
     * if credentials are missing. Use isConfigured() to guard calls in application code.
     *
     * @param  string|null  $username  cPanel username (CPANEL_USERNAME)
     * @param  string|null  $password  cPanel password (CPANEL_PASSWORD)
     * @param  string|null  $server  cPanel server URL, e.g. https://server.example.com:2083 (CPANEL_SERVER)
     */
    public function __construct(
        private readonly ?string $username,
        private readonly ?string $password,
        private readonly ?string $server,
    ) {}

    /**
     * Whether cPanel credentials are configured.
     *
     * Use this to guard calls in application code rather than checking config keys directly:
     *
     *   if (Cpanel::isConfigured()) {
     *       Cpanel::createEmail('user', 'password', 'example.com');
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
     * @throws \RuntimeException When cPanel credentials are not configured.
     */
    private function client(): PendingRequest
    {
        $credentials = $this->credentials();

        if (! $this->client) {
            $this->client = Http::baseUrl(rtrim($credentials['server'], '/'))
                ->withBasicAuth($credentials['username'], $credentials['password'])
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
                'cPanel is not configured. Set CPANEL_USERNAME, CPANEL_PASSWORD, and CPANEL_SERVER in your .env file.'
            );
        }

        return [
            'username' => $username,
            'password' => $password,
            'server' => $server,
        ];
    }

    /**
     * Create a new email account via cPanel UAPI.
     *
     * @link https://api.docs.cpanel.net/cpanel/uapi/email/add-pop/
     *
     * @param  string  $email  The local part of the email address (without @domain)
     * @param  string  $password  The email account password
     * @param  string|null  $domain  The domain (defaults to the account's primary domain)
     * @return array{status: string, code: int, output: mixed}
     */
    public function createEmail(string $email, string $password, ?string $domain = null): array
    {
        $params = array_filter([
            'email' => $email,
            'password' => $password,
            'domain' => $domain,
        ]);

        $response = $this->client()->get('/execute/Email/add_pop', $params)->json();

        if (! $response['status']) {
            return [
                'status' => 'error',
                'code' => 400,
                'output' => $response['errors'][0] ?? 'Unknown error occurred',
            ];
        }

        return [
            'status' => 'success',
            'code' => 200,
            'output' => $response['data'] ?? [],
        ];
    }

    /**
     * Delete an email account via cPanel UAPI.
     *
     * @link https://api.docs.cpanel.net/cpanel/uapi/email/delete-pop/
     *
     * @param  string  $email  The local part of the email address (without @domain)
     * @param  string|null  $domain  The domain (defaults to the account's primary domain)
     * @return array{status: string, code: int, output: mixed}
     */
    public function deleteEmail(string $email, ?string $domain = null): array
    {
        $params = array_filter([
            'email' => $email,
            'domain' => $domain,
        ]);

        $response = $this->client()->get('/execute/Email/delete_pop', $params)->json();

        if (! $response['status']) {
            return [
                'status' => 'error',
                'code' => 400,
                'output' => $response['errors'][0] ?? 'Unknown error occurred',
            ];
        }

        return [
            'status' => 'success',
            'code' => 200,
            'output' => $response['data'] ?? [],
        ];
    }

    /**
     * List email accounts via cPanel UAPI.
     *
     * @link https://api.docs.cpanel.net/cpanel/uapi/email/list-pops/
     *
     * @param  string|null  $domain  Filter by domain (defaults to all domains on the account)
     * @return array{status: string, code: int, output: mixed}
     */
    public function listEmails(?string $domain = null): array
    {
        $params = array_filter(['domain' => $domain]);

        $response = $this->client()->get('/execute/Email/list_pops', $params)->json();

        if (! $response['status']) {
            return [
                'status' => 'error',
                'code' => 400,
                'output' => $response['errors'][0] ?? 'Unknown error occurred',
            ];
        }

        return [
            'status' => 'success',
            'code' => 200,
            'output' => $response['data'] ?? [],
        ];
    }

    /**
     * Set the HTTP client (used for testing).
     */
    public function setClient(PendingRequest $client): void
    {
        $this->client = $client;
    }
}
