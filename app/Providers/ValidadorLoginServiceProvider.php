<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use \App\User;

class ValidadorLoginServiceProvider extends ServiceProvider {

    public function boot() {
        $this->app['validator']->extend('usuario', function ($attribute, $value)  {
            $usuarios = User::where('email', $value)->get();
            foreach ($usuarios as $usuario) {
                if ($usuario->permissao != Usuario::PERMISSAO_INATIVO)
                    return true;
            }
            return false;
        });
    }

    public function register(){}
}