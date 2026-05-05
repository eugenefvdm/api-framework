<?php

use Eugenefvdm\Api\Cpanel;
use Illuminate\Support\Facades\Http;

test('isConfigured returns true when credentials are present', function () {
    $cpanel = new Cpanel('user', 'pass', 'https://server.example.com:2083');

    expect($cpanel->isConfigured())->toBeTrue();
});

test('isConfigured returns false when credentials are absent', function () {
    $cpanel = new Cpanel(null, null, null);

    expect($cpanel->isConfigured())->toBeFalse();
});

test('any api call throws RuntimeException when not configured', function () {
    $cpanel = new Cpanel(null, null, null);

    $cpanel->listEmails();
})->throws(\RuntimeException::class, 'cPanel is not configured');

test('it can create an email account successfully', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../stubs/cpanel/create_email_success.json'), true);

    Http::fake([
        'server.example.com:2083/execute/Email/add_pop*' => Http::response($stub, 200),
    ]);

    $cpanel = new Cpanel('user', 'pass', 'https://server.example.com:2083');
    $result = $cpanel->createEmail('dash-test01', 'TestPass123!', 'eugenefvdm.com');

    expect($result['status'])->toBe('success')
        ->and($result['code'])->toBe(200)
        ->and($result['output'])->toBe('dash-test01+eugenefvdm.com');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/execute/Email/add_pop')
            && $request->method() === 'GET';
    });
});

test('it returns 400 when email account already exists', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../stubs/cpanel/create_email_already_exists.json'), true);

    Http::fake([
        'server.example.com:2083/execute/Email/add_pop*' => Http::response($stub, 200),
    ]);

    $cpanel = new Cpanel('user', 'pass', 'https://server.example.com:2083');
    $result = $cpanel->createEmail('dash-test01', 'TestPass123!', 'eugenefvdm.com');

    expect($result['status'])->toBe('error')
        ->and($result['code'])->toBe(400)
        ->and($result['output'])->toBe('The account dash-test01@eugenefvdm.com already exists!');
});

test('it can delete an email account successfully', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../stubs/cpanel/delete_email_success.json'), true);

    Http::fake([
        'server.example.com:2083/execute/Email/delete_pop*' => Http::response($stub, 200),
    ]);

    $cpanel = new Cpanel('user', 'pass', 'https://server.example.com:2083');
    $result = $cpanel->deleteEmail('dash-test01', 'eugenefvdm.com');

    expect($result['status'])->toBe('success')
        ->and($result['code'])->toBe(200);
});

test('it can list email accounts successfully', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../stubs/cpanel/list_emails_success.json'), true);

    Http::fake([
        'server.example.com:2083/execute/Email/list_pops*' => Http::response($stub, 200),
    ]);

    $cpanel = new Cpanel('user', 'pass', 'https://server.example.com:2083');
    $result = $cpanel->listEmails('eugenefvdm.com');

    expect($result['status'])->toBe('success')
        ->and($result['code'])->toBe(200)
        ->and($result['output'])->toHaveCount(2)
        ->and($result['output'][1]['email'])->toBe('dash-37ce@eugenefvdm.com');
});
