<?php

use Eugenefvdm\Api\Fail2ban;

// beforeEach(function () {
//     $this->fail2ban = new Fail2ban();
// });

// afterEach(function () {
//     Mockery::close();
// });

test('first method returns the first matching log entry', function () {
    $expectedOutput = [
        'status' => 'success',
        'output' => "2025-03-21 08:07:51,498 fail2ban.filter         [2557302]: INFO    [postfix-sasl] Found 196.15.204.89 - 2025-03-21 08:07:51",
    ];
    
    $fail2ban = Mockery::mock(Fail2ban::class)->makePartial()->shouldAllowMockingProtectedMethods();
    
    $fail2ban->shouldReceive('executeCommand')
        ->once()
        ->with("grep 196.15.204 /var/log/fail2ban.log.1 | head -n 1 || grep 196.15.204 /var/log/fail2ban.log | head -n 1")
        ->andReturn($expectedOutput);

    $fail2ban->setServer('testuser', 'testhost');
    $result = $fail2ban->first('196.15.204');

    expect($result)->toBe($expectedOutput);
});

test('last method returns the last matching log entry', function () {
    $expectedOutput = [
        'status' => 'success',
        'output' => "2025-03-25 10:49:21,440 fail2ban.filter         [1541019]: INFO    [postfix-sasl] Ignore 196.15.204.89 by ip",
    ];
    
    $fail2ban = Mockery::mock(Fail2ban::class)->makePartial()->shouldAllowMockingProtectedMethods();

    $fail2ban->shouldReceive('executeCommand')
        ->with("grep 196.15.204 /var/log/fail2ban.log | tail -n 1 || grep 196.15.204 /var/log/fail2ban.log.1 | tail -n 1")
        ->once()
        ->andReturn($expectedOutput);

    $fail2ban->setServer('testuser', 'testhost');
    $result = $fail2ban->last('196.15.204');

    expect($result)->toBe($expectedOutput);
}); 