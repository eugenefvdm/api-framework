<?php

namespace Eugenefvdm\Api\Contracts;

interface Fail2banInterface
{
    public function first(string $value): array;

    public function last(string $value): array;
}
