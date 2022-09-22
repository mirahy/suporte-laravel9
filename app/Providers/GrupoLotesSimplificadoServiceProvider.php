<?php

namespace App\Providers;

use App\Http\Controllers\GrupoLotesSimplificadoController;
use Illuminate\Support\ServiceProvider;

class GrupoLotesSimplificadoServiceProvider extends ServiceProvider
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
        $this->app->singleton('GrupoLotesSimplificadoService',function ($app) {
            return new GrupoLotesSimplificadoController();
        });
    }
}
