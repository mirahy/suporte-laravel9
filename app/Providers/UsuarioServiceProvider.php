<?php

namespace App\Providers;

use App\Http\Controllers\UsuarioController;
use Illuminate\Support\ServiceProvider;

class UsuarioServiceProvider extends ServiceProvider
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
        $this->app->singleton('UsuarioService',function ($app) {
            return new UsuarioController();
        });
    }
}
