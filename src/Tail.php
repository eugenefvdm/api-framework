<?php

namespace Eugenefvdm\Api;

class Tail extends AbstractServer
{
    public function last(string $value, int $count = 1): array
    {
        $command = "cat /var/log/mail.log | grep {$value} | tail -n {$count}";

        return $this->executeCommand($command);
    }
}
