<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServidorMoodle extends Model
{
    protected $table = 'servidores_moodle';

    protected $fillable = [
        'nome',
        'url',
        'ativo',
    ];

    protected $visible = [
        'id',
        'nome',
        'url',
        'ativo',
    ];
}
