<?php

namespace ImLiam\BladeHelper\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use ImLiam\BladeHelper\BladeHelperServiceProvider;

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
