<?php

use Eugenefvdm\Api\Whmcs;
use Illuminate\Support\Facades\Http;

test('it can add a new client successfully', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../stubs/whmcs/add_client_success.json'), true);

    Http::fake([
        'test.example.com/includes/api.php' => Http::response($stub, 200),
    ]);

    $api = new Whmcs('https://test.example.com', 'test_identifier', 'test_secret');

    $clientData = [
        'firstname' => 'John',
        'lastname' => 'Doe',
        'email' => 'john@example.com',
        'phonenumber' => '1234567890',
        'address1' => '123 Main St',
        'city' => 'New York',
        'state' => 'NY',
        'postcode' => '10001',
        'country' => 'US',
        'password2' => 'securepassword123',
    ];

    $result = $api->addClient($clientData);

    expect($result)->toBe($stub);
    expect($result['result'])->toBe('success');
    expect($result['clientid'])->toBe(354);
    expect($result['owner_id'])->toBe(8);    
});

test('it returns error when client already exists', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../stubs/whmcs/add_client_already_exists.json'), true);

    Http::fake([
        'test.example.com/includes/api.php' => Http::response($stub, 200),
    ]);

    $api = new Whmcs('https://test.example.com', 'test_identifier', 'test_secret');

    $clientData = [
        'firstname' => 'John',
        'lastname' => 'Doe',
        'email' => 'john@example.com',
        'phonenumber' => '1234567890',
        'address1' => '123 Main St',
        'city' => 'New York',
        'state' => 'NY',
        'postcode' => '10001',
        'country' => 'US',
        'password2' => 'securepassword123',
    ];

    $result = $api->addClient($clientData);

    expect($result)->toBe($stub);
    expect($result['result'])->toBe('error');
    expect($result['message'])->toBe('A user already exists with that email address');
}); 