<?php

namespace Eugenefvdm\Api\Contracts;

interface WhmInterface
{   
    public function bandwidth(): array; 
    public function suspendEmail(string $username, string $email): array;
    public function unsuspendEmail(string $username, string $email): array;    
}