<?php

namespace Eugenefvdm\Api;

use Eugenefvdm\Api\Contracts\WhmcsInterface;
use Eugenefvdm\Api\Models\ClientGroup;
use Eugenefvdm\Api\Models\CustomField;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Whmcs implements WhmcsInterface
{
    private string $url;

    private string $api_identifier;

    private string $api_secret;

    private array $mergeData;

    public function __construct(string $url, string $api_identifier, string $api_secret)
    {
        $this->url = $url;
        $this->api_identifier = $api_identifier;
        $this->api_secret = $api_secret;

        $this->throwExceptionIfUrlNotPresent();

        $this->mergeData = [
            'limitstart' => 0,
            'limitnum' => config('api.whmcs.limitnum'),
        ];
    }

    /**
     * Main entry point for all API calls
     */
    private function call(string $action, array $data = []): array
    {
        $postfields = [
            'identifier' => $this->api_identifier,
            'secret' => $this->api_secret,
            'action' => $action,
            'responsetype' => 'json',
        ];
        $postfields = array_merge($data, $this->mergeData, $postfields);

        // Display the input to the API call if debugging is enabled
        config('whmcs.debug') && ray($postfields);

        $apiUrl = $this->url.'/includes/api.php';

        $response = Http::withOptions(['verify' => false])
            ->asForm()
            ->post($apiUrl, $postfields);

        return $response->json();
    }

    /**
     * Add client
     *
     * @param array $data Required parameters:
     *                    - firstname (string) Client's first name
     *                    - lastname (string) Client's last name
     *                    - email (string) Client's email address
     *                    - phonenumber (string) Client's phone number
     *                    - address1 (string) Client's street address
     *                    - city (string) Client's city
     *                    - state (string) Client's state/province
     *                    - postcode (string) Client's postal code
     *                    - country (string) Client's country code (e.g. ZA)
     *                    - password2 (string) Client's password
     * @return array Response from the WHMCS API
     *
     * @see https://developers.whmcs.com/api-reference/addclient/
     */
    public function addClient(array $data): array
    {
        return $this->call('AddClient', $data);
    }

    /**
     * A Laravel model wrapper around `tblclientgroups`
     * @param string $name 
     * @param string $color 
     * @return void 
     */
    public function createClientGroup(string $name, string $color = '#ffffff'): void
    {        
        ClientGroup::create([
            'groupname' => $name,
            'groupcolour' => $color,
        ]);
    }

    /**
     * A Laravel model wrapper around `tblcustomfields`
     * @param string $name 
     * @param string $type 
     * @return void 
     */
    public function createCustomClientField(string $name, string $type = 'text'): void
    {
        CustomField::create([
            'type' => 'client',
            'fieldname' => $name,
            'fieldtype' => $type,
        ]);
    }

    /**
     * Throw exception if the API variables have not been set
     *
     * @throws Exception
     */
    private function throwExceptionIfUrlNotPresent(): void
    {
        if (! $this->url) {
            $error = 'The WHMCS API URL was not found. Please ensure the configuration has been published and check your environment for the following three variables: WHMCS_URL, WHMCS_API_IDENTIFIER, WHMCS_API_SECRET';

            // Log the error
            Log::error($error);

            throw new Exception($error);
        }
    }
}
