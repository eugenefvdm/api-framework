<?php

namespace Eugenefvdm\Api;

use SoapClient;
use SoapFault;

class Zadomains
{
    private SoapClient $client;

    private $username;

    private $password;

    private $wsdl = 'http://www.zadomains.net/api/API_GENERAL.asmx?WSDL';

    /**
     * Constructor
     *
     * @param  string  $username  Zadomains username
     * @param  string  $password  Zadomains password
     *
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
     * Set the SOAP client (used for testing)
     *
     * @param  SoapClient  $client  The SOAP client to use
     */
    public function setClient(SoapClient $client): void
    {
        $this->client = $client;
    }

    /**
     * Get registrant information for a domain
     *
     * @param  string  $domainName  The domain name to query
     * @return string Registrant email address
     *
     * @throws SoapFault
     */
    public function registrant(string $domainName): string
    {
        $result = $this->getDomainSelect($domainName);
        $data = json_decode($result->Domain_SelectResult, true);

        if (! isset($data['Response_Value'])) {
            throw new \RuntimeException('Unable to fetch registrant information');
        }

        return $data['Response_Value']['OwnerEmail'] ?? null;
    }

    /**
     * Get domain select info
     *
     * @param  string  $domainName  The domain name to query
     * @return mixed Response from the API
     *
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
     * @param  string  $domainName  The domain name to query
     * @return mixed Response from the API
     *
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
     * @param  string  $domainName  The domain name to query
     * @return mixed Response from the API
     *
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
