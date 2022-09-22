<?php

namespace App\Providers;

use App\Http\Controllers\SigecadController;
use Illuminate\Support\ServiceProvider;

class SigecadServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('SigecadService',function ($app) {
            return new SigecadController();
        });
    }
}
