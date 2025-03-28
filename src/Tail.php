<?php

namespace Eugenefvdm\Api;

class Tail extends Server
{
    public function last(string $value, int $count = 1): array
    {
        $command = "cat /var/log/mail.log | egrep -i '{$value}' | tail -n {$count}";

        return $this->executeCommand($command);
    }
}
