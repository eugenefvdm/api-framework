<?php

namespace Eugenefvdm\Api;

class Dns
{    
    public function MX(string $domain, $preferNative = false)
    {
        if (!$preferNative) {
            $results = shell_exec("dig +tries=2 +short MX $domain");

            $lines = explode("\n", $results);
    
            unset($lines[count($lines) - 1]); // The rid of last newline outputted on the command line

            return $lines;
        }

        return dns_get_record($domain, DNS_MX);
    }

    public function NS(string $domain) : array
    {
        $records = dns_get_record($domain, DNS_NS);
        $servers = [];
        
        if ($records) {
            foreach ($records as $record) {
                if (isset($record['target'])) {
                    $servers[] = $record['target'];
                }
            }
        }

        return $servers;
    }
}
