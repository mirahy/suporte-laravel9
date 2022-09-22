<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObjetivoSala extends Model
{
    protected $table = 'objetivo_salas';

    protected $fillable = [
        'sigla',
        'descricao',
        'visivel',
    ];

    protected $visible = [
        'id',
        'sigla',
        'descricao',
        'visivel',
    ];
}
