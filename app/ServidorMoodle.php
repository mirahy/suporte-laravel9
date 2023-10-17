<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServidorMoodle extends Model
{
    protected $table = 'servidores_moodle';

    protected $fillable = [
        'nome',
        'url',
        'nome_banco',
        'ip_banco',
        'ip_server',
        'prefixo',
        'status',
        'ativo',
    ];

    protected $visible = [
        'id',
        'nome',
        'url',
        'nome_banco',
        'ip_banco',
        'ip_server',
        'prefixo',
        'status',
        'ativo',
    ];
}
