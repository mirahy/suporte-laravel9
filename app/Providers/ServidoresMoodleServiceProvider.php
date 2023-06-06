<?php

namespace App\Providers;

use App\Http\Controllers\MessagesController;
use App\Http\Controllers\ServidoresMoodleController;
use Illuminate\Support\ServiceProvider;

class ServidoresMoodleServiceProvider extends ServiceProvider
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
        $this->app->singleton('ServidoresMoodleService',function ($app) {
            return new ServidoresMoodleController();
        });
    }
}
