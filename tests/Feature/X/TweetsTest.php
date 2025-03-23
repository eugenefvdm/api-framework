<?php

use Eugenefvdm\Api\X;
use Illuminate\Support\Facades\Http;

test('tweets returns user tweets with default limit', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../../stubs/x/tweets.json'), true);

    Http::fake([
        'api.twitter.com/2/users/12345678/tweets*' => Http::response($stub, 200),
    ]);

    $x = new X('test_bearer_token');

    $result = $x->tweets('12345678');

    expect($result)
        ->toBe($stub)
        ->and($result['data'])->toBeArray()
        ->and($result['data'])->toHaveCount(5)
        ->and($result['meta'])->toHaveKeys(['result_count', 'newest_id', 'oldest_id', 'next_token'])
        ->and($result['meta']['result_count'])->toBe(5);
});

test('tweets returns user tweets with custom limit', function () {
    $json = file_get_contents(__DIR__.'/../../stubs/x/tweets.json');
    $stub = json_decode($json, true);

    if (!is_array($stub) || !isset($stub['data']) || !isset($stub['meta'])) {
        throw new \RuntimeException('Invalid JSON structure in tweets.json stub');
    }

    // Create a new array with the modified data
    $modifiedStub = [
        'data' => array_slice($stub['data'], 0, 3),
        'meta' => array_merge($stub['meta'], ['result_count' => 3])
    ];

    Http::fake([
        'api.twitter.com/2/users/12345678/tweets*' => Http::response($modifiedStub, 200),
    ]);

    $x = new X('test_bearer_token');

    $result = $x->tweets('12345678', 3);

    expect($result)
        ->toBe($modifiedStub)
        ->and($result['data'])->toBeArray()
        ->and($result['data'])->toHaveCount(3)
        ->and($result['meta']['result_count'])->toBe(3);
}); 