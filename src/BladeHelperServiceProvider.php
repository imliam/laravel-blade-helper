<?php

namespace ImLiam\BladeHelper;

use Illuminate\Support\ServiceProvider;

class BladeHelperServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton('blade.helper', function () {
            return new BladeHelper($this->app->make('blade.compiler'));
        });
    }
}
