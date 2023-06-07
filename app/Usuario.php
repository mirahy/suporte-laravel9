<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Usuario extends Model
{
    use HasRoles;

    const PERMISSAO_ADMINISTRADOR = "ADMINISTRADOR";
    const PERMISSAO_PRESIDENTE = 'PRESIDENTE';
    const PERMISSAO_PATRIMONIO = 'PATRIMONIO';
    const PERMISSAO_MEMBRO = 'MEMBRO';
    const PERMISSAO_INATIVO = 'INATIVO';

    protected $table = 'usuario';

    //protected $guarded = ['id'];

    protected $fillable = [
        'nome',
        'login',
        'email',
        'permissao'
    ];

    protected $visible =  [
        'id',
        'nome',
        'login',
        'email',
        'permissao'
    ];

}
