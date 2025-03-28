<?php

namespace Eugenefvdm\Api;

class Dns
{
    /**
     * Get MX records for a domain using dig or PHP native
     * 
     * @param string $domain 
     * @param bool $useDig 
     * @return array 
     */
    public function MX(string $domain, bool $useDig = true): array
    {
        if ($useDig) {
            $results = shell_exec("dig +tries=2 +short MX $domain");
            
            if (!is_string($results)) {
                return [
                    'status' => 'error',
                    'output' => 'The shell_exec command dig command failed.'
                ];
            }

            $lines = explode("\n", $results);
            unset($lines[count($lines) - 1]); // The rid of last newline outputted on the command line

            return [
                'status' => 'success',
                'output' => $lines
            ];
        }

        $records = dns_get_record($domain, DNS_MX);
        if (empty($records)) {
            return [
                'status' => 'error',
                'output' => "dns_get_record didn't return any records."
            ];
        }
        
        return [
            'status' => 'success',
            'output' => $records
        ];
    }

    public function NS(string $domain): array
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

        if (empty($servers)) {
            return [
                'status' => 'error',
                'output' => "dns_get_record didn't return any 'target' records."
            ];
        }

        return [
            'status' => 'success',
            'output' => $servers
        ];
    }
}
