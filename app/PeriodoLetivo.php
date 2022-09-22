<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeriodoLetivo extends Model
{
    protected $table = 'periodo_letivos';

    protected $fillable = [
        'nome',
        'id_sigecad',
        'descricao',
        'sufixo',
        'inicio_auto_increment',
        'ativo'
    ];

    protected $visible = [
        'id',
        'nome',
        'id_sigecad',
        'descricao',
        'sufixo',
        'inicio_auto_increment',
        'ativo'
    ];
}
