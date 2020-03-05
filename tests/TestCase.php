<?php

namespace ImLiam\BladeHelper\Tests;

use ImLiam\BladeHelper\BladeHelperServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Add the package's service provider.
     *
     * @param $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [BladeHelperServiceProvider::class];
    }
}
