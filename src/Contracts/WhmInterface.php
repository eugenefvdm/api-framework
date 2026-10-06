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
     * List hosting plans (packages) available to the authenticated WHM user.
     *
     * @param  string  $want  Package scope: all, creatable, editable, or viewable
     * @return array{status: string, code: int, output: list<array<string, mixed>>, reason?: string}
     */
    public function listPackages(string $want = 'all'): array;

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

    /**
     * Look up one DNS zone on this server.
     *
     * Point the client at a DNS cluster member. A synchronized cluster already
     * holds every member's zones, so this is a single parse_dns_zone call.
     *
     * @return array{domain: string, payload: list<mixed>}|null
     */
    public function findDnsZone(string $domain): ?array;

    public static function generatePassword(): string;
}
