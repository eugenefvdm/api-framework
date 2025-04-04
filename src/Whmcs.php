<?php

namespace Eugenefvdm\Api;

use Eugenefvdm\Api\Contracts\WhmcsInterface;
use Eugenefvdm\Api\Models\CustomField;
use Eugenefvdm\Api\Models\ClientGroup;
use PDO;

class Whmcs implements WhmcsInterface
{
    private ?string $url;
    private ?string $api_identifier;
    private ?string $api_secret;
    private string $database_name;
    private string $database_username;
    private string $database_password;

    public function __construct(private $client)
    {
        $this->url = $client['url'] ?? null;
        $this->api_identifier = $client['api_identifier'] ?? null;
        $this->api_secret = $client['api_secret'] ?? null;
        $this->database_name = $client['database_name'];
        $this->database_username = $client['database_username'];
        $this->database_password = $client['database_password'];
    }

    public function createClientGroup(string $name, string $color = '#ffffff'): void {
        ClientGroup::create([
            'groupname' => $name,
            'groupcolour' => $color,
        ]);
    }

    public function createCustomClientField(string $name, string $type = 'text'): void {
        CustomField::create([
            'type' => 'client',
            'fieldname' => $name,
            'fieldtype' => $type,
        ]);
    }
}
