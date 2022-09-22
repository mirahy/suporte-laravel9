<?php

namespace App\Providers;

use App\Http\Controllers\SalaController;
use Illuminate\Support\ServiceProvider;

class SalaServiceProvider extends ServiceProvider
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
        $this->app->singleton('SalaService',function ($app) {
            return new SalaController();
        });
    }
}
