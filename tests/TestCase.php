<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    

    protected function getUserAdmin() {
        $user = factory(User::class)->make();
        $user->permissao = User::PERMISSAO_ADMINISTRADOR;
        return $user;
    }

    protected function getUserBadeco() {
        $user = factory(User::class)->make();
        $user->permissao = User::PERMISSAO_USUARIO;
        return $user;
    }
}
