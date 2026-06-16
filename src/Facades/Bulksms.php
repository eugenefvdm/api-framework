<?php

namespace Eugenefvdm\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Eugenefvdm\Api\Bulksms encoding(string $encoding)
 * @method static array sendSms(string $message, string|array $recipients)
 * @method static array sendUnicodeSms(string $message, string|array $recipients)
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
