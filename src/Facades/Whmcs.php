<?php

namespace Eugenefvdm\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array addClient(array $data): array
 * @method static void createClientGroup(string $name, string $color = '#ffffff'): void
 * @method static void createCustomClientField(string $name, string $type = 'text'): void
 *
 * @see \Eugenefvdm\Api\Whmcs
 */
class Whmcs extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'whmcs';
    }
}
