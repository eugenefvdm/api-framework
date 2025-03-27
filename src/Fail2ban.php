<?php

namespace Eugenefvdm\Api;

class Fail2ban extends AbstractServer
{
    public function first(string $value): array
    {
        $command = "grep {$value} /var/log/fail2ban.log.1 || grep {$value} /var/log/fail2ban.log";

        return $this->executeCommand($command);
    }

    public function last(string $value): array
    {
        $command = "grep {$value} /var/log/fail2ban.log | tail -n 1 || grep {$value} /var/log/fail2ban.log.1 | tail -n 1";

        return $this->executeCommand($command);
    }
}
