<?php

use Eugenefvdm\Api\Discord;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

test('getUser returns user information', function () {
    $stub = json_decode(file_get_contents(__DIR__ . '/../../stubs/public/discord/user/get_user_success.json'), true);
    
    // Create a mock response
    $mock = new MockHandler([
        new Response(200, [], json_encode($stub))
    ]);
    
    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);
    
    // Create Discord instance with test token and mock client
    $discord = new Discord('test_bot_token', $client);
    
    $result = $discord->getUser('123456789012345678');
    
    expect($result)
        ->toBe($stub)
        ->and($result['id'])->toBe('123456789012345678')
        ->and($result['username'])->toBe('username')
        ->and($result['global_name'])->toBe('Eugene van der Merwe')
        ->and($result['avatar'])->toBe('12345678901234567890123456789012');
}); 