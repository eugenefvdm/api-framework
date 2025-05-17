<?php

namespace Eugenefvdm\Api\Contracts;

interface WhmInterface
{
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

    public static function generatePassword(): string;
}
