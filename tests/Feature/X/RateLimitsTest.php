<?php

use Eugenefvdm\Api\X;
use Illuminate\Support\Facades\Http;

test('userWithRateLimits returns user data and rate limits', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../../stubs/x/user_id.json'), true);

    Http::fake([
        'api.twitter.com/2/users/by/username/eugenefvdm' => Http::response($stub, 200, [
            'x-rate-limit-limit' => '1200000',
            'x-rate-limit-remaining' => '1199998',
            'x-rate-limit-reset' => '1738820163',
        ]),
    ]);

    $x = new X('test_bearer_token');

    $result = $x->userWithRateLimits('eugenefvdm');

    expect($result)
        ->toHaveKeys(['data', 'rate_limits'])
        ->and($result['data'])->toBe($stub)
        ->and($result['rate_limits'])->toHaveKeys(['limit', 'remaining', 'reset'])
        ->and($result['rate_limits']['limit'])->toBe('1200000')
        ->and($result['rate_limits']['remaining'])->toBe('1199998')
        ->and($result['rate_limits']['reset'])->toBe('1738820163');
}); 