<?php

use Eugenefvdm\Api\Whm;
use Illuminate\Support\Facades\Http;

test('bandwidth returns bandwidth information', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../../stubs/whm/bandwidth_success.json'), true);

    Http::fake([
        'test.example.com:2087/json-api/showbw' => Http::response($stub, 200),
    ]);

    $api = new Whm('test_user', 'test_pass', 'https://test.example.com:2087');

    $result = $api->bandwidth();

    expect($result)->toBe($stub);
    expect($result['bandwidth'][0]['acct'])->toHaveCount(2);
    expect($result['bandwidth'][0]['acct'][0]['maindomain'])->toBe('example1.com');
    expect($result['bandwidth'][0]['acct'][0]['totalbytes'])->toBe(7021740625);
    expect($result['bandwidth'][0]['acct'][1]['maindomain'])->toBe('example2.com');
    expect($result['bandwidth'][0]['acct'][1]['totalbytes'])->toBe(8179292839);


    Http::assertSent(function ($request) {
        return $request->url() === 'https://test.example.com:2087/json-api/showbw'
            && $request->method() === 'GET'
            && $request->header('Authorization')[0] === 'WHM test_user:test_pass';
    });
}); 