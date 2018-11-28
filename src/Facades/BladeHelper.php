<?php

namespace ImLiam\BladeHelper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ImLiam\BladeHelper\BladeHelper
 */
class BladeHelper extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'blade.helper';
    }
}
