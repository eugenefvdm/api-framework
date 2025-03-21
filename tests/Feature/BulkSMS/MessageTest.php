<?php

use Eugenefvdm\Api\BulkSMS;
use Illuminate\Support\Facades\Http;

test('sendSMS successfully sends a message', function () {
    Http::fake([
        'bulksms.2way.co.za/*' => Http::response('0|IN_PROGRESS|2065473445', 200),
    ]);

    // Create BulkSMS instance with test credentials
    $bulkSms = new BulkSMS('test_user', 'test_pass');

    $result = $bulkSms->sendSMS('Hello!', ['27823096710']);

    expect($result['27823096710'])->toHaveKeys(['success', 'details', 'http_status_code', 'api_status_code', 'api_message', 'api_batch_id'])
        ->and($result['27823096710']['success'])->toBe(1)
        ->and($result['27823096710']['http_status_code'])->toBe(200)
        ->and($result['27823096710']['api_status_code'])->toBe('0')
        ->and($result['27823096710']['api_message'])->toBe('IN_PROGRESS')
        ->and($result['27823096710']['api_batch_id'])->toBe('2065473445');
});
