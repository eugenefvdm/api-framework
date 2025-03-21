<?php

namespace Eugenefvdm\Api;

use SoapClient;
use SoapFault;

class ZADomains
{
    // ... existing code ...

    /**
     * Get registrant information for a domain in a simplified format
     *
     * @param string $domainName The domain name to query
     * @return array Simplified registrant information
     * @throws SoapFault
     */
    public function registrant(string $domainName): array
    {
        $result = $this->getDomainSelect($domainName);
        $data = json_decode($result->Domain_SelectResult, true);
        
        if (!isset($data['Response_Value'])) {
            throw new \RuntimeException('Unable to fetch registrant information');
        }

        return [
            'email' => $data['Response_Value']['OwnerEmail'] ?? null,
            'name' => $data['Response_Value']['OwnerName'] ?? null,
            'company' => $data['Response_Value']['OwnerCompany'] ?? null,
            'phone' => $data['Response_Value']['OwnerTelephone'] ?? null,
        ];
    }
} 