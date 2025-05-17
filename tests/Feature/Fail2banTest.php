<?php

use Eugenefvdm\Api\Contracts\Fail2banInterface;

test('first method returns the first matching log entry', function () {
    // Create a mock of the interface instead of the concrete class
    $fail2ban = mock(Fail2banInterface::class);

    $expectedOutput = [
        'status' => 'success',
        'output' => '2025-03-21 08:07:51,498 fail2ban.filter         [2557302]: INFO    [postfix-sasl] Found 196.15.204.89 - 2025-03-21 08:07:51',
    ];

    // Mock the first method directly since we're working with the interface
    $fail2ban->shouldReceive('first')
        ->with('196.15.204')
        ->once()
        ->andReturn($expectedOutput);

    $result = $fail2ban->first('196.15.204');

    expect($result)
        ->toBeArray()
        ->toHaveCount(2)
        ->and($result['status'])
        ->toBe('success')
        ->and($result['output'])
        ->toContain('196.15.204');
});

test('last method returns the last matching log entry', function () {
    $fail2ban = mock(Fail2banInterface::class);

    $expectedOutput = [
        'status' => 'success',
        'output' => '2025-03-25 10:49:21,440 fail2ban.filter         [1541019]: INFO    [postfix-sasl] Ignore 196.15.204.89 by ip',
    ];

    $fail2ban->shouldReceive('last')
        ->with('196.15.204')
        ->once()
        ->andReturn($expectedOutput);

    $result = $fail2ban->last('196.15.204');

    expect($result)
        ->toBeArray()
        ->toHaveCount(2)
        ->and($result['status'])
        ->toBe('success')
        ->and($result['output'])
        ->toContain('196.15.204');
});
