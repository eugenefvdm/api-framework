<?php

namespace Eugenefvdm\Api\Contracts;

interface ServerInterface
{
    /**
     * Set the server configuration for the Fail2ban service
     */
    public function setServer(string $username, string $hostname, int $port = 22): self;
}
