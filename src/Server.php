<?php

namespace Eugenefvdm\Api;

use Eugenefvdm\Api\Contracts\ServerInterface;

class ServerConfig
{
    public function __construct(
        public readonly string $username,
        public readonly string $hostname,
        public readonly int $ssh_port,
    ) {}
}

abstract class Server implements ServerInterface
{
    protected ?ServerConfig $server = null;

    public function setServer(string $username, string $hostname, int $port = 22): self
    {
        $this->server = new ServerConfig($username, $hostname, $port);

        return $this;
    }
    
    protected function executeCommand(string $command): array
    {       
        if (!$this->server) {
            return [
                'status' => 'error',
                'output' => 'Server configuration not set. Use setServer() first.',
            ];
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
            'output' => $process->getErrorOutput(),
        ];
    }
}
