<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faculdade extends Model
{
    protected $table = 'faculdades';


    protected $fillable = [
        'sigla',
        'nome',
        'auto_increment_ref',
        'ativo'
    ];

    protected $visible = [
        'id',
        'sigla',
        'nome',
        'auto_increment_ref',
        'ativo',
        'cursos'
    ];

    protected $appends = array('cursos');
    public function getCursosAttribute($value) {
        return Curso::where('faculdade_id', $this->id)->get();
    }
}
