<?php

use Eugenefvdm\Api\X;
use Illuminate\Support\Facades\Http;

test('userId returns user data', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../../stubs/x/user_id.json'), true);

    Http::fake([
        'api.twitter.com/2/users/by/username/joebloggs' => Http::response($stub, 200),
    ]);

    $x = new X('test_bearer_token');

    $result = $x->userId('joebloggs');

    expect($result)
        ->toBe($stub)
        ->and($result['data'])->toHaveKeys(['id', 'name', 'username'])
        ->and($result['data']['id'])->toBe('12345678')
        ->and($result['data']['name'])->toBe('Joe Bloggs')
        ->and($result['data']['username'])->toBe('joebloggs');
});
