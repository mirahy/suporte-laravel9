<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable2;
use Illuminate\Contracts\Auth\Authenticatable;

class User extends Authenticatable2 implements Authenticatable
{
    use Notifiable;

    const PERMISSAO_ADMINISTRADOR = "ADMINISTRADOR";
    const PERMISSAO_USUARIO = 'USUARIO';
    const PERMISSAO_INATIVO = 'INATIVO';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'permissao'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * muitos para muitos
     */
    public function gestorRecursos()
    {
        return $this->belongsToMany('App\Recurso', 'gestores_recursos','user_id','recurso_id');
    }

    public function isAdmin() {
        return $this->permissao == self::PERMISSAO_ADMINISTRADOR;
    }

    public function isUser() {
        return $this->permissao == self::PERMISSAO_USUARIO;
    }

    public function isInative() {
        return $this->permissao == self::PERMISSAO_INATIVO;
    }
}
