<?php

use Eugenefvdm\Api\BulkSMS;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

test('sendSMS successfully sends a message', function () {
    // Create a mock response
    $mock = new MockHandler([
        new Response(200, [], '0|IN_PROGRESS|2065473445'),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    // Create BulkSMS instance with test credentials and mock client
    $bulkSms = new BulkSMS('test_user', 'test_pass', $client);

    $result = $bulkSms->sendSMS('Hello!', ['27823096710']);

    expect($result['27823096710'])->toHaveKeys(['success', 'details', 'http_status_code', 'api_status_code', 'api_message', 'api_batch_id'])
        ->and($result['27823096710']['success'])->toBe(1)
        ->and($result['27823096710']['http_status_code'])->toBe(200)
        ->and($result['27823096710']['api_status_code'])->toBe('0')
        ->and($result['27823096710']['api_message'])->toBe('IN_PROGRESS')
        ->and($result['27823096710']['api_batch_id'])->toBe('2065473445');
});
