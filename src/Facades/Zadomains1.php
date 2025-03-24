<?php

namespace Eugenefvdm\Api\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array registrant(string $domainName)
 * @method static mixed getDomainSelect(string $domainName)
 * @method static mixed getDomainSelectInfo(string $domainName)
 * @method static mixed getDomainSelectAllByContact(string $contactName)
 *
 * @see \Eugenefvdm\Api\Zadomains
 */
class Zadomains extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'zadomains';
    }
}
