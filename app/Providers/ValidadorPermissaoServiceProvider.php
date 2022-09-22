<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use \App\User;

class ValidadorPermissaoServiceProvider extends ServiceProvider {

    public function boot() {
        $this->app['validator']->extend('permissao', function ($attribute, $value)  {
            switch ($value) {
                case User::PERMISSAO_ADMINISTRADOR:
                case User::PERMISSAO_USUARIO:
                case User::PERMISSAO_INATIVO:
                    return true;
            }
            return false;
        });
    }

    public function register(){}
}