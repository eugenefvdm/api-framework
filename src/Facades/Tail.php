<?php

namespace Eugenefvdm\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Eugenefvdm\Api\Tail setServer(string $username, string $hostname, int $port = 22)
 * @method static array last(string $value, int $count = 1)
 *
 * @see \Eugenefvdm\Api\Tail
 */
class Tail extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tail';
    }
}
