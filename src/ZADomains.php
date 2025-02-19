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
     * Get domain select info
     *
     * @param string $domainName The domain name to query
     * @return mixed Response from the API
     * @throws SoapFault
     */
    public function getDomainSelectInfo($domainName)
    {
        $params = [
            'zadomains_username' => $this->username,
            'zadomains_password' => $this->password,
            'domainname' => $domainName,
        ];

        return $this->client->Domain_Select_Info($params);
    }

    /**
     * Get domain select
     *
     * @param string $domainName The domain name to query
     * @return mixed Response from the API
     * @throws SoapFault
     */
    public function getDomainSelect($domainName)
    {
        $params = [
            'zadomains_username' => $this->username,
            'zadomains_password' => $this->password,
            'domainname' => $domainName,
        ];

        return $this->client->Domain_Select($params);
    }

    /**
     * Get domain select all by contact
     *
     * @param string $domainName The domain name to query
     * @return mixed Response from the API
     * @throws SoapFault
     */
    public function getDomainSelectAllByContact($domainName)
    {
        $params = [
            'zadomains_username' => $this->username,
            'zadomains_password' => $this->password,
            'contactname' => $domainName,
        ];

        return $this->client->Domain_SelectAll_ByContact($params);
    }
} 