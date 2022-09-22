<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modalidade extends Model
{
    protected $table = 'modalidades';

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
