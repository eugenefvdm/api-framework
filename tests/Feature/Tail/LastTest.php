<?php

use Eugenefvdm\Api\Tail;
use Mockery;

test('last returns mail log entries when server is configured', function () {
    $stub = file_get_contents(__DIR__.'/../../stubs/tail/last_success.txt');

    $mockProcess = Mockery::mock('Spatie\Ssh\Process');
    $mockProcess->shouldReceive('isSuccessful')->once()->andReturn(true);
    $mockProcess->shouldReceive('getOutput')->once()->andReturn($stub);

    $mockSsh = Mockery::mock('Spatie\Ssh\Ssh');
    $mockSsh->shouldReceive('create')
        ->once()
        ->with('test_user', 'test_host', 22)
        ->andReturn($mockSsh);
    $mockSsh->shouldReceive('execute')
        ->once()
        ->with('cat /var/log/mail.log | grep storm@vander.host | tail -n 1')
        ->andReturn($mockProcess);

    $tail = new Tail;
    $tail->setServer('test_user', 'test_host', 22);

    $result = $tail->last('storm@vander.host');

    expect($result)
        ->toBeArray()
        ->toHaveKeys(['status', 'output'])
        ->and($result['status'])->toBe('success')
        ->and($result['output'])->toBe($stub);
});

test('last returns error when server is not configured', function () {
    $tail = new Tail;

    $result = $tail->last('storm@vander.host');

    expect($result)
        ->toBeArray()
        ->toHaveKeys(['status', 'error'])
        ->and($result['status'])->toBe('error')
        ->and($result['error'])->toBe('Server configuration not set. Use setServer() first.');
});

test('last returns error when command fails', function () {
    $mockProcess = Mockery::mock('Spatie\Ssh\Process');
    $mockProcess->shouldReceive('isSuccessful')->once()->andReturn(false);
    $mockProcess->shouldReceive('getErrorOutput')->once()->andReturn('Permission denied');

    $mockSsh = Mockery::mock('Spatie\Ssh\Ssh');
    $mockSsh->shouldReceive('create')
        ->once()
        ->with('test_user', 'test_host', 22)
        ->andReturn($mockSsh);
    $mockSsh->shouldReceive('execute')
        ->once()
        ->with('cat /var/log/mail.log | grep storm@vander.host | tail -n 1')
        ->andReturn($mockProcess);

    $tail = new Tail;
    $tail->setServer('test_user', 'test_host', 22);

    $result = $tail->last('storm@vander.host');

    expect($result)
        ->toBeArray()
        ->toHaveKeys(['status', 'error'])
        ->and($result['status'])->toBe('error')
        ->and($result['error'])->toBe('Permission denied');
});
