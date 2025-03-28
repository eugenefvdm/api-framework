<?php

namespace Eugenefvdm\Api;

use Eugenefvdm\Api\Contracts\Fail2banInterface;

class Fail2ban extends Server implements Fail2banInterface
{
    public function first(string $value): array
    {
        $command = "egrep -i '{$value}' /var/log/fail2ban.log.1 | head -n 1 || egrep -i '{$value}' /var/log/fail2ban.log | head -n 1";

        return $this->executeCommand($command);
    }

    public function last(string $value): array
    {
        $command = "egrep -i '{$value}' /var/log/fail2ban.log | tail -n 1 || egrep -i '{$value}' /var/log/fail2ban.log.1 | tail -n 1";

        return $this->executeCommand($command);
    }
}
