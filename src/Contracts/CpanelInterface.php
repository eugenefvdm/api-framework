<?php

namespace Eugenefvdm\Api\Contracts;

interface CpanelInterface
{
    public function isConfigured(): bool;

    public function createEmail(string $email, string $password, ?string $domain = null): array;

    public function deleteEmail(string $email, ?string $domain = null): array;

    public function listEmails(?string $domain = null): array;
}
