<?php

use Eugenefvdm\Api\Hellopeter;
use Illuminate\Support\Facades\Http;

test('unrepliedReviews returns empty reviews list', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../../stubs/hellopeter/reviews/get_unreplied_empty.json'), true);

    Http::fake([
        'https://api.hellopeter.com/v5/api/reviews*' => Http::response($stub, 200),
    ]);

    $helloPeter = new Hellopeter('test_api_key');

    $result = $helloPeter->unrepliedReviews();

    expect($result)
        ->toBe($stub)
        ->and($result['meta'])->toHaveKeys(['perPage', 'current', 'from', 'to', 'totalRecords', 'previousPage', 'lastPage', 'nextPage'])
        ->and($result['meta']['perPage'])->toBe(10)
        ->and($result['meta']['totalRecords'])->toBe(0)
        ->and($result['data'])->toBeArray()
        ->and($result['data'])->toBeEmpty();
});
