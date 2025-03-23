<?php

use Eugenefvdm\Api\X;
use Illuminate\Support\Facades\Http;

test('userWithRateLimits returns user data and rate limits', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../../stubs/x/user_id.json'), true);

    Http::fake([
        'api.twitter.com/2/users/by/username/joebloggs' => Http::response($stub, 200, [
            'api-version' => '2.131',
            'date' => 'Sun, 23 Mar 2025 17:15:24 GMT',
            'x-rate-limit-limit' => '1200000',
            'x-rate-limit-remaining' => '1199998',
            'x-rate-limit-reset' => '1738820163',
            'x-app-limit-24hour-limit' => '1200000',
            'x-app-limit-24hour-remaining' => '1199998',
            'x-app-limit-24hour-reset' => '1738820163',
            'x-response-time' => '80',
        ]),
    ]);

    $x = new X('test_bearer_token');

    $result = $x->userWithRateLimits('joebloggs');

    expect($result)
        ->toHaveKeys(['data', 'headers'])
        ->and($result['data'])->toBe($stub)
        ->and($result['headers'])->toHaveKeys([
            'api-version',
            'date',
            'x-rate-limit-limit',
            'x-rate-limit-remaining',
            'x-rate-limit-reset',
            'x-app-limit-24hour-limit',
            'x-app-limit-24hour-remaining',
        ])
        ->and($result['headers']['date'])->toBe('Sun, 23 Mar 2025 17:15:24 GMT')
        ->and($result['headers']['x-rate-limit-limit'])->toBe('1200000')
        ->and($result['headers']['x-rate-limit-remaining'])->toBe('1199998')
        ->and($result['headers']['x-rate-limit-reset'])->toBe('1738820163')
        ->and($result['headers']['x-app-limit-24hour-limit'])->toBe('1200000')
        ->and($result['headers']['x-app-limit-24hour-remaining'])->toBe('1199998')
        ->and($result['headers']['x-app-limit-24hour-reset'])->toBe('1738820163')
        ->and($result['headers']['x-response-time'])->toBe('80');
}); 