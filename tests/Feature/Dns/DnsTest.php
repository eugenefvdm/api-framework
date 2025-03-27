<?php

use Eugenefvdm\Api\Dns;

test('nsRecords returns nameserver records for a domain', function () {
    $dns = new Dns;

    $records = $dns->NS('example.com');

    expect($records)
        ->toBeArray()
        ->and($records)
        ->each->toBeString();
});

test('nsRecords returns empty array for non-existent domain', function () {
    $dns = new Dns;

    $records = $dns->NS('nonexistent-domain-that-should-not-exist.com');

    expect($records)
        ->toBeArray()
        ->toBeEmpty();
});
