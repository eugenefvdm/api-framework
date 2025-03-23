<?php

use Eugenefvdm\Api\Discord;
use Illuminate\Support\Facades\Http;

test('getUser returns user information', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../../stubs/discord/user/get_user_success.json'), true);

    Http::fake([
        'discord.com/api/v10/users/*' => Http::response($stub, 200),
    ]);

    // Create Discord instance with test token
    $discord = new Discord('test_bot_token');

    $result = $discord->user('123456789012345678');

    expect($result)
        ->toBe($stub)
        ->and($result['id'])->toBe('123456789012345678')
        ->and($result['username'])->toBe('username')
        ->and($result['global_name'])->toBe('Eugene van der Merwe')
        ->and($result['avatar'])->toBe('12345678901234567890123456789012');
});
