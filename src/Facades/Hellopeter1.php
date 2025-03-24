<?php

namespace Eugenefvdm\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getUnrepliedReviews()
 *
 * @see \Eugenefvdm\Api\Hellopeter
 */
class Hellopeter extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'hellopeter';
    }
}
