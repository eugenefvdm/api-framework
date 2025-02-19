<?php

namespace Eugenevdm;

use SoapClient;
use SoapFault;

class ZADomains
{
    private $client;
    private $username;
    private $password;
    private $wsdl = 'http://www.zadomains.net/api/API_GENERAL.asmx?WSDL';

    /**
     * Constructor
     *
     * @param string $username ZADomains username
     * @param string $password ZADomains password
     * @throws SoapFault
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        
        $this->client = new SoapClient($this->wsdl, [
            'trace' => true,
            'exceptions' => true,
        ]);
    }

    /**
     * Get domain information
     *
     * @param string $domainName The domain name to query
     * @return mixed Response from the API
     * @throws SoapFault
     */
    public function getDomainInfo($domainName)
    {
        $params = [
            'zadomains_username' => $this->username,
            'zadomains_password' => $this->password,
            'domainname' => $domainName,
        ];

        return $this->client->Domain_Select_Info($params);
    }
} 