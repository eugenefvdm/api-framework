<?php

namespace Eugenefvdm\Api\Contracts;

interface WhmcsInterface
{   
    public function createCustomClientField(
        string $fieldname, string $fieldtype = 'text'
        ): void;
}