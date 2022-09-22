<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $table = 'cursos';

    protected $fillable = [
        'nome',
        'auto_increment_ref',
        'faculdade_id',
        'curso_key',
        'ativo'
    ];

    protected $visible = [
        'id',
        'nome',
        'auto_increment_ref',
        'faculdade_id',
        'curso_key',
        'ativo'
    ];

    public function faculdade()
    {
        return $this->belongsTo('App\Faculdade');
    }
}
