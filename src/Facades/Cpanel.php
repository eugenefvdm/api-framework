<?php

namespace Eugenefvdm\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isConfigured()
 * @method static array createEmail(string $email, string $password, ?string $domain = null)
 * @method static array deleteEmail(string $email, ?string $domain = null)
 * @method static array listEmails(?string $domain = null)
 *
 * @see \Eugenefvdm\Api\Cpanel
 */
class Cpanel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'cpanel';
    }
}
