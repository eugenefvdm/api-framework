<?php

namespace Eugenefvdm\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array first(string $value)
 * @method static array last(string $value)
 *
 * @see \Eugenefvdm\Api\Fail2ban
 */
class Fail2ban extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'fail2ban';
    }
}
