<?php

namespace App\Providers;

use App\Http\Controllers\MessagesController;
use Illuminate\Support\ServiceProvider;

class MessagesServiceProvider extends ServiceProvider
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
        $this->app->singleton('MessagesService',function ($app) {
            return new MessagesController();
        });
    }
}
