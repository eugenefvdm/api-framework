<?php

namespace Eugenefvdm\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed sendSMS(string $message, array $recipients)
 *
 * @see \Eugenefvdm\Api\Bulksms
 */
class Bulksms extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'bulksms';
    }
}
