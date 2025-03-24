<?php

namespace Eugenefvdm\Api;

class Dns
{    
    public function NS(string $domain) : array
    {
        $records = dns_get_record($domain, DNS_NS);
        $nameServers = [];
        
        if ($records) {
            foreach ($records as $record) {
                if (isset($record['target'])) {
                    $nameServers[] = $record['target'];
                }
            }
        }

        return $nameServers;
    }
}
