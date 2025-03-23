<?php

use Eugenefvdm\Api\X;
use Illuminate\Support\Facades\Http;

test('tweets returns rate limit error when limit is hit', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../../stubs/x/rate_limit.json'), true);

    Http::fake([
        'api.twitter.com/2/users/12345678/tweets*' => Http::response($stub, 429),
    ]);

    $x = new X('test_bearer_token');

    $result = $x->tweets('12345678');

    expect($result)
        ->toBe($stub)
        ->and($result['title'])->toBe('Too Many Requests')
        ->and($result['detail'])->toBe('Too Many Requests')
        ->and($result['type'])->toBe('about:blank')
        ->and($result['status'])->toBe(429);
}); 