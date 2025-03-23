<?php

namespace Eugenefvdm\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array bandwidth()
 *
 * @see \Eugenefvdm\Api\Whm
 */
class Whm extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'whm';
    }
} 