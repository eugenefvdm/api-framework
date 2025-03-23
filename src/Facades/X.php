<?php

namespace Eugenefvdm\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array userId(string $username)
 * @method static array tweets(string $userId, int $maxResults = 5)
 * @method static array userWithRateLimits(string $username)
 *
 * @see \Eugenefvdm\Api\X
 */
class X extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'x';
    }
} 