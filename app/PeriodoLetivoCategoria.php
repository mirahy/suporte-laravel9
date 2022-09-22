<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeriodoLetivoCategoria extends Model
{
    protected $table = 'periodo_letivos_categorias';

    protected $fillable = [
        'curso_id',
        'periodo_letivo_id',
        'categoria_id'
    ];

    protected $visible = [
        'id',
        'curso_id',
        'periodo_letivo_id',
        'categoria_id'
    ];


    public function curso()
    {
        return $this->belongsTo('App\Curso');
    }

    public function periodoLetivo()
    {
        return $this->belongsTo('App\PeriodoLetivo');
    }
}
