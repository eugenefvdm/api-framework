<?php

namespace Eugenefvdm\Api\Contracts;

interface WhmInterface
{
    public function isConfigured(): bool;

    public function bandwidth(): array;

    public function suspendEmail(string $username, string $email): array;

    public function unsuspendEmail(string $username, string $email): array;

    public function cphulkBlacklist(): array;

    public function cphulkWhitelist(): array;

    public function createEmail(
        string $cpanelUsername,
        string $email,
        string $password,
        ?string $domain = null,
        ?int $quota = null,
        bool $sendWelcomeEmail = false
    ): array;

    public function deleteEmail(string $cpanelUsername, string $email): array;

    /**
     * Park (alias) a domain onto an existing web virtual host.
     *
     * @return array{status: string, code: int, output: mixed, reason?: string}
     */
    public function parkDomain(string $domain, string $username, string $webVhostDomain): array;

    /**
     * Return the cPanel username that owns a domain, or null if not found.
     */
    public function domainOwner(string $domain): ?string;

    /**
     * List parked domains (aliases) for a cPanel account.
     *
     * @return array{status: string, code: int, output: list<array<string, mixed>>}
     */
    public function listParkedDomains(string $cpanelUsername, ?string $regex = null): array;

    /**
     * Search for a parked domain on the server (optionally scoped to one account).
     *
     * @return array{domain: string, username: string, status?: string, dir?: string, reldir?: string}|null
     */
    public function findParkedDomain(string $domain, ?string $cpanelUsername = null): ?array;

    public static function generatePassword(): string;
}
