<?php

namespace Eugenefvdm\Api\Contracts;

interface TailInterface
{
    public function last(string $value, int $count = 1): array;
} 