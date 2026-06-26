<?php

use Eugenefvdm\Api\X;
use Illuminate\Support\Facades\Http;

it('posts a tweet with a signed OAuth 1.0a request', function () {
    Http::fake([
        'api.twitter.com/2/tweets' => Http::response([
            'data' => [
                'id' => '2070581086178963845',
                'edit_history_tweet_ids' => ['2070581086178963845'],
                'text' => 'Hello from the API framework',
            ],
        ], 201),
    ]);

    $x = new X('bearer', 'consumer_key', 'consumer_secret', 'access_token', 'access_token_secret');

    $result = $x->tweet('Hello from the API framework');

    expect($result['status'])->toBe('success')
        ->and($result['output']['data']['id'])->toBe('2070581086178963845')
        ->and($result['output']['data']['text'])->toBe('Hello from the API framework');

    Http::assertSent(function ($request) {
        $authorization = $request->header('Authorization')[0] ?? '';

        return $request->url() === 'https://api.twitter.com/2/tweets'
            && $request->method() === 'POST'
            && str_starts_with($authorization, 'OAuth ')
            && str_contains($authorization, 'oauth_signature=')
            && $request['text'] === 'Hello from the API framework';
    });
});

it('returns an error when OAuth 1.0a credentials are missing', function () {
    Http::fake();

    $x = new X('bearer');

    $result = $x->tweet('This should not be sent');

    expect($result['status'])->toBe('error')
        ->and($result['output'])->toContain('OAuth 1.0a credentials');

    Http::assertNothingSent();
});

it('returns an error when the X API rejects the post', function () {
    Http::fake([
        'api.twitter.com/2/tweets' => Http::response([
            'title' => 'Forbidden',
            'detail' => 'Your app is not permitted to create a Tweet.',
            'type' => 'about:blank',
            'status' => 403,
        ], 403),
    ]);

    $x = new X('bearer', 'consumer_key', 'consumer_secret', 'access_token', 'access_token_secret');

    $result = $x->tweet('Hello from the API framework');

    expect($result['status'])->toBe('error')
        ->and($result['output']['status'])->toBe(403)
        ->and($result['output']['title'])->toBe('Forbidden');
});
