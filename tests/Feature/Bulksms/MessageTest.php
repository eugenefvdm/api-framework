<?php

use Eugenefvdm\Api\ApiServiceProvider;
use Eugenefvdm\Api\Bulksms;
use Eugenefvdm\Api\Facades\Bulksms as BulksmsFacade;
use Eugenefvdm\Api\HellopeterReviewSms;
use Eugenefvdm\Api\SmsText;
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

it('sends unicode sms messages with the convenience method', function () {
    Http::fake([
        'bulksms.2way.co.za/*' => Http::response('0|IN_PROGRESS|1234567890', 200),
    ]);

    $bulkSms = new Bulksms('test_user', 'test_pass');
    $message = 'Hello ⭐️';

    $bulkSms->sendUnicodeSms($message, ['27823096710']);

    Http::assertSent(function ($request) use ($message) {
        return $request['message'] === bin2hex(mb_convert_encoding($message, 'UCS-2BE', 'UTF-8'))
            && $request['dca'] === '16bit';
    });
});

it('can use a temporary encoding without changing the default sender', function () {
    Http::fake([
        'bulksms.2way.co.za/*' => Http::response('0|IN_PROGRESS|1234567890', 200),
    ]);

    $bulkSms = new Bulksms('test_user', 'test_pass');
    $message = 'Hello ⭐️';

    $bulkSms->encoding('16bit')->sendSms($message, ['27823096710']);
    $bulkSms->sendSms('Hello again', ['27823096711']);

    Http::assertSent(function ($request) use ($message) {
        return $request['msisdn'] === '27823096710'
            && $request['message'] === bin2hex(mb_convert_encoding($message, 'UCS-2BE', 'UTF-8'))
            && $request['dca'] === '16bit';
    });

    Http::assertSent(function ($request) {
        return $request['msisdn'] === '27823096711'
            && $request['message'] === 'Hello again'
            && ($request['dca'] ?? null) === null;
    });
});

it('can choose unicode automatically when the message is not gsm safe', function () {
    Http::fake([
        'bulksms.2way.co.za/*' => Http::response('0|IN_PROGRESS|1234567890', 200),
    ]);

    $bulkSms = new Bulksms('test_user', 'test_pass', 'auto');
    $unicodeMessage = 'Hello ⭐️';

    $bulkSms->sendSms('Plain text', ['27823096710']);
    $bulkSms->sendSms($unicodeMessage, ['27823096711']);

    Http::assertSent(function ($request) {
        return $request['msisdn'] === '27823096710'
            && $request['message'] === 'Plain text'
            && ($request['dca'] ?? null) === null;
    });

    Http::assertSent(function ($request) use ($unicodeMessage) {
        return $request['msisdn'] === '27823096711'
            && $request['message'] === bin2hex(mb_convert_encoding($unicodeMessage, 'UCS-2BE', 'UTF-8'))
            && $request['dca'] === '16bit';
    });
});

it('uses configured encoding for facade sms messages', function () {
    config()->set('api.bulksms.username', 'test_user');
    config()->set('api.bulksms.password', 'test_pass');
    config()->set('api.bulksms.encoding', '16bit');

    app()->register(ApiServiceProvider::class);
    app()->forgetInstance(Bulksms::class);
    BulksmsFacade::clearResolvedInstance('bulksms');

    Http::fake([
        'bulksms.2way.co.za/*' => Http::response('0|IN_PROGRESS|1234567890', 200),
    ]);

    $message = 'Hello ⭐️';

    BulksmsFacade::sendSms($message, ['27823096710']);

    Http::assertSent(function ($request) use ($message) {
        return $request['message'] === bin2hex(mb_convert_encoding($message, 'UCS-2BE', 'UTF-8'))
            && $request['dca'] === '16bit';
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

it('can limit sms text by encoding and message parts', function () {
    $unicode = str_repeat('⭐', 80);
    $sevenBit = str_repeat('{', 80);

    expect(SmsText::encoding('Plain text'))->toBe('7bit')
        ->and(SmsText::encoding('Hello ⭐'))->toBe('16bit')
        ->and(SmsText::length(SmsText::limit($unicode, encoding: '16bit'), '16bit'))->toBe(70)
        ->and(SmsText::length(SmsText::limit($sevenBit, encoding: '7bit'), '7bit'))->toBe(160);
});
