<?php

namespace Eugenefvdm\Api;

use Eugenefvdm\Api\Contracts\TailInterface;

class Tail extends Server implements TailInterface
{
    public function last(string $value, int $count = 1): array
    {
        $command = "cat /var/log/mail.log | egrep -i '{$value}' | tail -n {$count}";

        return $this->executeCommand($command);
    }
}
