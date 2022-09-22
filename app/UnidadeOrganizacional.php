<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnidadeOrganizacional extends Model
{
    protected $table = 'unidades_organizacionais';

    protected $fillable = [
        'nome',
        'valor',
    ];

    protected $visible = [
        'id',
        'nome',
        'valor',
    ];
}
