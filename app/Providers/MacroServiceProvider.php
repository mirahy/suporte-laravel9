<?php

namespace App\Providers;

use App\Http\Controllers\MacroController;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
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
         $this->app->singleton('MacroService',function ($app) {
            return new MacroController();
        });
    }
}
