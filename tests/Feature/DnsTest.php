<?php

use Eugenefvdm\Api\Dns;

test('nsRecords returns nameserver records for a domain', function () {
    $dns = new Dns;

    $response = $dns->NS('example.com');

    expect($response)
        ->toBeArray()
        ->toHaveKeys(['status', 'output'])
        ->and($response['status'])
        ->toBe('success')
        ->and($response['output'])
        ->toBeArray()
        ->each->toBeString();
});

test('nsRecords returns error for non-existent domain', function () {
    $dns = new Dns;

    $response = $dns->NS('nonexistent-domain-that-should-not-exist.com');

    expect($response)
        ->toBeArray()
        ->toHaveKeys(['status', 'output'])
        ->and($response['status'])
        ->toBe('error')
        ->and($response['output'])
        ->toBeString();
});

test('mxRecords returns mx records for a domain', function () {
    $dns = new Dns;

    $response = $dns->MX('example.com');

    expect($response)
        ->toBeArray()
        ->toHaveKeys(['status', 'output'])
        ->and($response['status'])
        ->toBe('success')
        ->and($response['output'])
        ->toBeArray();
});

test('mxRecords returns error for non-existent domain', function () {
    $dns = new Dns;

    $response = $dns->MX('nonexistent-domain-that-should-not-exist.com');

    ray($response);

    expect($response)
        ->toBeArray()
        ->toHaveKeys(['status', 'output'])
        ->and($response['status'])
        ->toBe('error')
        ->and($response['output'])
        ->toBeString();
});

test('mxRecords with dig returns mx records for a domain', function () {
    $dns = new Dns;

    $response = $dns->MX('example.com', true);

    expect($response)
        ->toBeArray()
        ->toHaveKeys(['status', 'output'])
        ->and($response['status'])
        ->toBe('success')
        ->and($response['output'])
        ->toBeArray();
});
