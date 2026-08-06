<?php

use Eugenefvdm\Api\Contracts\WhmInterface;
use Eugenefvdm\Api\Whm;
use Illuminate\Support\Facades\Http;

test('bandwidth returns bandwidth information', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../stubs/whm/bandwidth_success.json'), true);

    Http::fake([
        'test.example.com:2087/json-api/showbw' => Http::response($stub, 200),
    ]);

    $api = new Whm('test_user', 'test_pass', 'https://test.example.com:2087');

    $result = $api->bandwidth();

    expect($result)->toBe($stub);
    expect($result['bandwidth'][0]['acct'])->toHaveCount(2);
    expect($result['bandwidth'][0]['acct'][0]['maindomain'])->toBe('example1.com');
    expect($result['bandwidth'][0]['acct'][0]['totalbytes'])->toBe(7021740625);
    expect($result['bandwidth'][0]['acct'][1]['maindomain'])->toBe('example2.com');
    expect($result['bandwidth'][0]['acct'][1]['totalbytes'])->toBe(8179292839);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://test.example.com:2087/json-api/showbw'
            && $request->method() === 'GET'
            && $request->header('Authorization')[0] === 'WHM test_user:test_pass';
    });
});

test('it can suspend an email account successfully', function () {
    $whm = mock(WhmInterface::class);

    $whm->shouldReceive('suspendEmail')
        ->with('username', 'user@example.com')
        ->andReturn([
            'status' => 'success',
            'code' => 200,
            'output' => [],
        ]);

    $result = $whm->suspendEmail('username', 'user@example.com');

    expect($result['status'])->toBe('success');
    expect($result['code'])->toBe(200);
    expect($result['output'])->toBe([]);
});

test('it returns 404 when email account does not exist', function () {
    $whm = mock(WhmInterface::class);

    $whm->shouldReceive('suspendEmail')
        ->with('username', 'user@example.com')
        ->andReturn([
            'status' => 'error',
            'code' => 404,
            'output' => "Email address 'user@example.com' not found",
        ]);

    $result = $whm->suspendEmail('username', 'user@example.com');

    expect($result['status'])->toBe('error');
    expect($result['code'])->toBe(404);
    expect($result['output'])->toBe("Email address 'user@example.com' not found");
});

test('it returns 400 when email is already suspended', function () {
    $whm = mock(WhmInterface::class);

    $whm->shouldReceive('suspendEmail')
        ->with('username', 'user@example.com')
        ->andReturn([
            'status' => 'error',
            'code' => 400,
            'output' => 'Logins for "user@example.com" are suspended.',
        ]);

    $result = $whm->suspendEmail('username', 'user@example.com');

    expect($result['status'])->toBe('error');
    expect($result['code'])->toBe(400);
    expect($result['output'])->toBe('Logins for "user@example.com" are suspended.');
});

test('it can get cPHulk whitelist records successfully', function () {
    $whm = mock(WhmInterface::class);

    $whm->shouldReceive('cphulkWhitelist')
        ->andReturn(json_decode(file_get_contents(__DIR__.'/../stubs/whm/whitelist_success.json'), true));

    $result = $whm->cphulkWhitelist();

    expect($result['data']['ips_in_list'])->toHaveCount(10);
    expect($result['data']['ips_in_list'])->toHaveKey('1.2.3.4');
    expect($result['data']['ips_in_list'])->toHaveKey('4.5.106.198');
});

test('it can get cPHulk blacklist records successfully', function () {
    $whm = mock(WhmInterface::class);

    $whm->shouldReceive('cphulkBlacklist')
        ->andReturn(json_decode(file_get_contents(__DIR__.'/../stubs/whm/blacklist_success.json'), true));

    $result = $whm->cphulkBlacklist();

    expect($result['data']['ips_in_list'])->toHaveCount(10);
    expect($result['data']['ips_in_list'])->toHaveKey('1.2.3.4');
    expect($result['data']['ips_in_list'])->toHaveKey('4.5.106.198');
});

test('it can create an email account successfully', function () {
    $whm = mock(WhmInterface::class);

    $whm->shouldReceive('createEmail')
        ->andReturn(json_decode(file_get_contents(__DIR__.'/../stubs/whm/create_email_success.json'), true));

    $result = $whm->createEmail('username', 'user', 'password123');

    expect($result['status'])->toBe('success');
    expect($result['code'])->toBe(200);
    expect($result['output'])->toBe('user+example.com');
});

test('it returns 400 when email account already exists', function () {
    $whm = mock(WhmInterface::class);

    $whm->shouldReceive('createEmail')
        ->andReturn(json_decode(file_get_contents(__DIR__.'/../stubs/whm/create_email_already_exists.json'), true));

    $result = $whm->createEmail('username', 'user', 'password123');

    expect($result['status'])->toBe('error');
    expect($result['code'])->toBe(400);
    expect($result['output'])->toBe('The account user@example.com already exists!');
});

test('it returns 400 when password strength is too weak', function () {
    $whm = mock(WhmInterface::class);

    $whm->shouldReceive('createEmail')
        ->andReturn(json_decode(file_get_contents(__DIR__.'/../stubs/whm/create_email_password_strengh_issue.json'), true));

    $result = $whm->createEmail('username', 'user', 'weakpass');

    expect($result['status'])->toBe('error');
    expect($result['code'])->toBe(400);
    expect($result['output'])->toContain('The password that you entered has a strength rating of');
});

test('generatePassword returns a 12 character string', function () {
    $password = Whm::generatePassword();

    expect($password)->toBeString()
        ->toHaveLength(12);
});

test('isConfigured returns true when credentials are present', function () {
    $whm = new Whm('user', 'pass', 'https://server.example.com:2087');

    expect($whm->isConfigured())->toBeTrue();
});

test('isConfigured returns false when credentials are absent', function () {
    $whm = new Whm(null, null, null);

    expect($whm->isConfigured())->toBeFalse();
});

test('any api call throws RuntimeException when not configured', function () {
    $whm = new Whm(null, null, null);

    $whm->bandwidth();
})->throws(\RuntimeException::class, 'WHM is not configured');

test('it can delete an email account successfully', function () {
    $whm = mock(WhmInterface::class);

    $whm->shouldReceive('deleteEmail')
        ->with('cpaneluser', 'user@example.com')
        ->andReturn([
            'status' => 'success',
            'code' => 200,
            'output' => [],
        ]);

    $result = $whm->deleteEmail('cpaneluser', 'user@example.com');

    expect($result['status'])->toBe('success');
    expect($result['code'])->toBe(200);
});

it('lists hosting packages via listpkgs', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../stubs/whm/list_packages_success.json'), true);

    Http::fake([
        'test.example.com:2087/json-api/listpkgs*' => Http::response($stub, 200),
    ]);

    $api = new Whm('root', 'token', 'https://test.example.com:2087');

    $result = $api->listPackages();

    expect($result['status'])->toBe('success')
        ->and($result['code'])->toBe(200)
        ->and($result['output'])->toHaveCount(2)
        ->and($result['output'][0]['name'])->toBe('basic')
        ->and($result['output'][1]['name'])->toBe('pro');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/json-api/listpkgs')
            && $request['api.version'] == 1
            && $request['want'] === 'all';
    });
});

it('returns an error when listpkgs is denied', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../stubs/whm/list_packages_failure.json'), true);

    Http::fake([
        'test.example.com:2087/json-api/listpkgs*' => Http::response($stub, 200),
    ]);

    $api = new Whm('root', 'token', 'https://test.example.com:2087');

    $result = $api->listPackages('creatable');

    expect($result['status'])->toBe('error')
        ->and($result['code'])->toBe(400)
        ->and($result['output'])->toBe([])
        ->and($result['reason'])->toBe('Access denied');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/json-api/listpkgs')
            && $request['want'] === 'creatable';
    });
});

test('parkDomain parks a domain via create_parked_domain_for_user', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../stubs/whm/park_domain_success.json'), true);

    Http::fake([
        'test.example.com:2087/json-api/create_parked_domain_for_user*' => Http::response($stub, 200),
    ]);

    $api = new Whm('root', 'token', 'https://test.example.com:2087');

    $result = $api->parkDomain('dripread.co.za', 'park', 'park.vander.host');

    expect($result['status'])->toBe('success')
        ->and($result['code'])->toBe(200)
        ->and($result['reason'])->toBe('OK');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/json-api/create_parked_domain_for_user')
            && $request['domain'] === 'dripread.co.za'
            && $request['username'] === 'park'
            && $request['web_vhost_domain'] === 'park.vander.host'
            && $request['api.version'] == 1;
    });
});

test('parkDomain returns an error when WHM rejects the park request', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../stubs/whm/park_domain_failure.json'), true);

    Http::fake([
        'test.example.com:2087/json-api/create_parked_domain_for_user*' => Http::response($stub, 200),
    ]);

    $api = new Whm('root', 'token', 'https://test.example.com:2087');

    $result = $api->parkDomain('dripread.co.za', 'park', 'park.vander.host');

    expect($result['status'])->toBe('error')
        ->and($result['code'])->toBe(400)
        ->and($result['output'])->toContain('already configured');
});

test('domainOwner returns the cPanel username for a domain', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../stubs/whm/getdomainowner_success.json'), true);

    Http::fake([
        'test.example.com:2087/json-api/getdomainowner*' => Http::response($stub, 200),
    ]);

    $api = new Whm('root', 'token', 'https://test.example.com:2087');

    expect($api->domainOwner('park.vander.host'))->toBe('park');
});

test('domainOwner returns null when the domain is not on the server', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../stubs/whm/getdomainowner_missing.json'), true);

    Http::fake([
        'test.example.com:2087/json-api/getdomainowner*' => Http::response($stub, 200),
    ]);

    $api = new Whm('root', 'token', 'https://test.example.com:2087');

    expect($api->domainOwner('missing.example.com'))->toBeNull();
});

test('listParkedDomains returns parked domains for an account', function () {
    $stub = json_decode(file_get_contents(__DIR__.'/../stubs/whm/list_parked_domains_success.json'), true);

    Http::fake([
        'test.example.com:2087/json-api/cpanel*' => Http::response($stub, 200),
    ]);

    $api = new Whm('root', 'token', 'https://test.example.com:2087');

    $result = $api->listParkedDomains('park', 'dripread');

    expect($result['status'])->toBe('success')
        ->and($result['output'])->toHaveCount(2)
        ->and($result['output'][0]['domain'])->toBe('dripread.co.za');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/json-api/cpanel')
            && $request['cpanel_jsonapi_module'] === 'Park'
            && $request['cpanel_jsonapi_func'] === 'listparkeddomains'
            && $request['cpanel_jsonapi_user'] === 'park'
            && $request['regex'] === 'dripread';
    });
});

test('findParkedDomain returns a match using domain owner lookup', function () {
    Http::fake([
        'test.example.com:2087/json-api/getdomainowner*' => Http::response(
            json_decode(file_get_contents(__DIR__.'/../stubs/whm/getdomainowner_success.json'), true),
            200
        ),
        'test.example.com:2087/json-api/cpanel*' => Http::response(
            json_decode(file_get_contents(__DIR__.'/../stubs/whm/list_parked_domains_success.json'), true),
            200
        ),
    ]);

    $api = new Whm('root', 'token', 'https://test.example.com:2087');

    $result = $api->findParkedDomain('dripread.co.za');

    expect($result)->toBeArray()
        ->and($result['domain'])->toBe('dripread.co.za')
        ->and($result['username'])->toBe('park')
        ->and($result['status'])->toBe('not redirected');
});

test('findParkedDomain returns null when the domain is not parked', function () {
    Http::fake([
        'test.example.com:2087/json-api/getdomainowner*' => Http::response(
            json_decode(file_get_contents(__DIR__.'/../stubs/whm/getdomainowner_missing.json'), true),
            200
        ),
    ]);

    $api = new Whm('root', 'token', 'https://test.example.com:2087');

    expect($api->findParkedDomain('missing.example.com'))->toBeNull();
});
