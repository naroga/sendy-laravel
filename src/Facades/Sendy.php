<?php

namespace Naroga\Sendy\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Sendy
 *
 * @package Naroga\Sendy\Facades
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
        return \Naroga\Sendy\Sendy::class;
    }
}
