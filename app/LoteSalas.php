<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoteSalas extends Model
{

    protected $table = 'lote_salas';


    protected $fillable = [
        'descricao',
        'periodo_letivo_id',
        'faculdade_id',
        'curso_id', 
        'is_salas_criadas',
        'is_estudantes_inseridos'
    ];

    protected $visible = [
        'id',
        'descricao',
        'periodo_letivo_id',
        'faculdade_id',
        'curso_id', 
        'is_salas_criadas',
        'is_estudantes_inseridos'
    ];

    public function salas()
    { 
        return $this->hasMany(Sala::class);
    }
}
