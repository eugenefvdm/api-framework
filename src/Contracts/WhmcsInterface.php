<?php

namespace Eugenefvdm\Api\Contracts;

interface WhmcsInterface
{
    public function addClient(array $data): array;

    public function createClientGroup(string $name, string $color = '#ffffff'): void;

    public function createCustomClientField(string $name, string $type = 'text'): void;
}
