<?php

namespace Eugenefvdm\Api\Tests\Feature\Slack;

use Eugenefvdm\Api\Slack;
use Illuminate\Support\Facades\Http;

test('sendMessage returns true on successful webhook call', function () {
    // Fake HTTP client
    Http::fake([
        '*' => Http::response('ok', 200)
    ]);
    
    $slack = new Slack('https://hooks.slack.com/services/test/webhook');
    
    $result = $slack->sendText('Hello testing from API collection');
    
    // Assert the message was sent successfully
    expect($result)->toBeTrue();
    
    // Assert the request was made with correct data
    Http::assertSent(function ($request) {
        return $request->url() === 'https://hooks.slack.com/services/test/webhook'
            && $request->method() === 'POST'
            && $request['text'] === 'Hello testing from API collection';
    });
});

test('sendMessage returns false on failed webhook call', function () {
    // Fake HTTP client with error response
    Http::fake([
        '*' => Http::response('', 400)
    ]);
    
    $slack = new Slack('https://hooks.slack.com/services/test/webhook');
    
    $result = $slack->sendText('Hello testing from API collection');
    
    // Assert the message was not sent successfully
    expect($result)->toBeFalse();
    
    // Assert the request was made
    Http::assertSent(function ($request) {
        return $request->url() === 'https://hooks.slack.com/services/test/webhook'
            && $request->method() === 'POST'
            && $request['text'] === 'Hello testing from API collection';
    });
}); 