<?php

namespace App\Providers;

use App\Http\Controllers\SuperMacroController;
use Illuminate\Support\ServiceProvider;

class SuperMacroServiceProvider extends ServiceProvider
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
        $this->app->singleton('SuperMacroService',function ($app) {
            return new SuperMacroController();
        });
    }
}
