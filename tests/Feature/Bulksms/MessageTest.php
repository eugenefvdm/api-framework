<?php

use Eugenefvdm\Api\Bulksms;
use Eugenefvdm\Api\HellopeterReviewSms;
use Illuminate\Support\Facades\Http;

it('sends an sms message successfully', function () {
    Http::fake([
        'bulksms.2way.co.za/*' => Http::response('0|IN_PROGRESS|1234567890', 200),
    ]);

    $bulkSms = new Bulksms('test_user', 'test_pass');

    $result = $bulkSms->sendSms('Hello!', ['27823096710']);

    expect($result['27823096710'])->toHaveKeys([
        'success',
        'details',
        'http_status_code',
        'api_status_code',
        'api_message',
        'api_batch_id',
    ])
        ->and($result['27823096710']['success'])->toBe(1)
        ->and($result['27823096710']['http_status_code'])->toBe(200)
        ->and($result['27823096710']['api_status_code'])->toBe('0')
        ->and($result['27823096710']['api_message'])->toBe('IN_PROGRESS')
        ->and($result['27823096710']['api_batch_id'])->toBe('1234567890');
});

it('sends seven bit messages without app specific rewriting', function () {
    Http::fake([
        'bulksms.2way.co.za/*' => Http::response('0|IN_PROGRESS|1234567890', 200),
    ]);

    $bulkSms = new Bulksms('test_user', 'test_pass');

    $bulkSms->sendSms('You received a ⭐️⭐️⭐️⭐️⭐️ review', ['27823096710']);

    Http::assertSent(function ($request) {
        return $request['message'] === 'You received a ⭐️⭐️⭐️⭐️⭐️ review'
            && ($request['dca'] ?? null) === null;
    });
});

it('sends unicode sms messages as sixteen bit payloads', function () {
    Http::fake([
        'bulksms.2way.co.za/*' => Http::response('0|IN_PROGRESS|1234567890', 200),
    ]);

    $bulkSms = new Bulksms('test_user', 'test_pass', '16bit');
    $message = 'Hello ⭐️';
    $expectedMessage = bin2hex(mb_convert_encoding($message, 'UCS-2BE', 'UTF-8'));

    $bulkSms->sendSms($message, ['27823096710']);

    Http::assertSent(function ($request) use ($expectedMessage) {
        return $request['message'] === $expectedMessage
            && $request['dca'] === '16bit'
            && ($request['allow_concat_text_sms'] ?? null) === null;
    });
});

it('does not shorten hellopeter unicode review sms messages by default', function () {
    Http::fake([
        'bulksms.2way.co.za/*' => Http::response('0|IN_PROGRESS|1234567890', 200),
    ]);

    $bulkSms = new Bulksms('test_user', 'test_pass', '16bit');
    $message = 'You received a ⭐️⭐️⭐️⭐️⭐️ review by Eugene at Hellopeter. Please reply ASAP.';

    $bulkSms->sendSms($message, ['27823096710']);

    Http::assertSent(function ($request) use ($message) {
        return $request['message'] === bin2hex(mb_convert_encoding($message, 'UCS-2BE', 'UTF-8'))
            && $request['dca'] === '16bit';
    });
});

it('shortens hellopeter review sms messages before using the generic sender', function () {
    Http::fake([
        'bulksms.2way.co.za/*' => Http::response('0|IN_PROGRESS|1234567890', 200),
    ]);

    $bulkSms = new Bulksms('test_user', 'test_pass', '16bit');
    $message = HellopeterReviewSms::shorten(
        'You received a ⭐️⭐️⭐️⭐️⭐️ review by Eugene at Hellopeter. Please reply ASAP.'
    );

    expect($message)->toBe('⭐️⭐️⭐️⭐️⭐️ review by Eugene @ Hellopeter. Reply ASAP.');

    $bulkSms->sendSms($message, ['27823096710']);

    Http::assertSent(function ($request) use ($message) {
        return $request['message'] === bin2hex(mb_convert_encoding($message, 'UCS-2BE', 'UTF-8'))
            && $request['dca'] === '16bit';
    });
});

it('can make hellopeter star ratings readable for seven bit sms messages', function () {
    $message = HellopeterReviewSms::starText('You received a ⭐️⭐️⭐️⭐️⭐️ review');

    expect($message)->toBe('You received a 5 star review');
});
