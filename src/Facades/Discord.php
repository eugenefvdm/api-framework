<?php

namespace Eugenefvdm\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getUser(string $userId)
 * @method static mixed sendMessage(string $message)
 * 
 * @see \Eugenefvdm\Api\Discord
 */
class Discord extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'discord';
    }
} 