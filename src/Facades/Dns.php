<?php

namespace Eugenefvdm\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *
 * @see \Eugenefvdm\Api\Dns
 */
class Dns extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'dns';
    }
}
