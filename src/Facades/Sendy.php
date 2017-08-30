<?php

namespace BuddyAd\Sendy\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Sendy
 *
 * @package BuddyAd\Sendy\Facades
 */
class Sendy extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return self::class;
    }
}
