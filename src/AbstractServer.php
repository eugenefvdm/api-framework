<?php

namespace Eugenefvdm\Api;

use Eugenefvdm\Api\Contracts\ServerInterface;

abstract class AbstractServer implements ServerInterface
{
    protected ?object $server = null;

    public function setServer(string $username, string $hostname, int $port = 22): self
    {
        $this->server = (object) [
            'username' => $username,
            'hostname' => $hostname,
            'ssh_port' => $port,
        ];

        return $this;
    }

    protected function validateServer(): array
    {
        if (! $this->server) {
            return [
                'status' => 'error',
                'error' => 'Server configuration not set. Use setServer() first.',
            ];
        }

        return [];
    }

    protected function executeCommand(string $command): array
    {
        $validation = $this->validateServer();
        if (! empty($validation)) {
            return $validation;
        }

        $process = \Spatie\Ssh\Ssh::create($this->server->username, $this->server->hostname, $this->server->ssh_port)
            ->execute($command);

        if ($process->isSuccessful()) {
            return [
                'status' => 'success',
                'output' => $process->getOutput(),
            ];
        }

        return [
            'status' => 'error',
            'error' => $process->getErrorOutput(),
        ];
    }
}
