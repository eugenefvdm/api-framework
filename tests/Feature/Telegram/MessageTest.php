<?php

use Eugenefvdm\Api\Telegram;
use Illuminate\Support\Facades\Http;

test('sendMessage successfully sends a message to Telegram', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../../stubs/public/telegram/message/send_success.json'), true);

    // Fake HTTP client
    Http::fake([
        'https://api.telegram.org/bot*/*' => Http::response($stub, 200),
    ]);

    $telegram = new Telegram('test_bot_token', '-1002458843320');

    $result = $telegram->sendMessage('Hi!');

    // Assert the response structure and values
    expect($result)
        ->toBeArray()
        ->toHaveKey('ok')
        ->toHaveKey('result')
        ->and($result['ok'])->toBeTrue()
        ->and($result['result'])->toHaveKeys([
            'message_id',
            'from',
            'chat',
            'date',
            'text',
        ])
        ->and($result['result']['text'])->toBe('Hi!')
        ->and($result['result']['from']['username'])->toBe('hello_peter_bot')
        ->and($result['result']['chat']['title'])->toBe('Eugene Telegram Test Group');

    // Assert the request was made with correct data
    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'https://api.telegram.org/bot')
            && $request->method() === 'POST'
            && $request['text'] === 'Hi!'
            && $request['parse_mode'] === 'HTML';
    });
});

test('sendMessage handles API error response', function () {
    // Fake HTTP client with error response
    Http::fake([
        'https://api.telegram.org/bot*/*' => Http::response([
            'ok' => false,
            'error_code' => 401,
            'description' => 'Unauthorized',
        ], 401),
    ]);

    $telegram = new Telegram('invalid_token', '-1002458843320');

    $result = $telegram->sendMessage('Hi!');

    // Assert the error response
    expect($result)
        ->toBeArray()
        ->toHaveKey('ok')
        ->toHaveKey('error_code')
        ->toHaveKey('description')
        ->and($result['ok'])->toBeFalse()
        ->and($result['error_code'])->toBe(401)
        ->and($result['description'])->toBe('Unauthorized');

    // Assert the request was attempted
    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'https://api.telegram.org/bot')
            && $request->method() === 'POST'
            && $request['text'] === 'Hi!'
            && $request['parse_mode'] === 'HTML';
    });
});
