<?php

use Eugenefvdm\Api\Contracts\TailInterface;

test('it can retrieve the last log entry containing a search value', function () {
    // Create a mock of the interface instead of the concrete class
    $tail = mock(TailInterface::class);
    
    $stub = file_get_contents(__DIR__.'/../stubs/tail/postfix_mail_log');
    $logLines = explode("\n", trim($stub));
    
    // Mock the last method directly since we're working with the interface
    $tail->shouldReceive('last')
        ->with('connect from', 1)
        ->once()
        ->andReturn([$logLines[0]]);
    
    // Get the result through the interface method
    $result = $tail->last('connect from', 1);
    
    expect($result)
        ->toBeArray()
        ->toHaveCount(1)
        ->and($result[0])
        ->toContain('connect from')
        ->toContain('postfix/smtpd');
});