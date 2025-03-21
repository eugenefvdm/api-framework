<?php

use Eugenefvdm\Api\ZADomains;

test('registrant returns owner email address', function () {
    $stub = json_decode(file_get_contents(__DIR__ . '/../../stubs/public/zadomains/domain/registrant_success.json'), true);
    
    $mockSoapClient = Mockery::mock('SoapClient');
    $mockSoapClient->shouldReceive('Domain_Select')
        ->once()
        ->with([
            'zadomains_username' => 'test_user',
            'zadomains_password' => 'test_pass',
            'domainname' => 'fintechsystems.co.za'
        ])
        ->andReturn((object)[
            'Domain_SelectResult' => json_encode($stub)
        ]);

    $api = new class('test_user', 'test_pass') extends ZADomains {
        protected function createSoapClient($wsdl, $options) {
            return Mockery::mock('SoapClient');
        }
    };
    
    $api->setClient($mockSoapClient);
    
    $email = $api->registrant('fintechsystems.co.za');
    
    expect($email)->toBe('user@example.com');
}); 