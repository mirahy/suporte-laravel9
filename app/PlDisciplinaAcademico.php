<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlDisciplinaAcademico extends Model
{
    protected $table = 'pl_disciplinas_academicos';

    protected $fillable = [
        'curso_id',
        'periodo_letivo_id',
        'disciplina',
        'estudantes',
        'disciplina_key',
        'carga_horaria_total_disciplina',
        'turma_id',
        'turma_nome',
        'avaliacao',
    ];

    protected $visible = [
        'id',
        'curso_id',
        'periodo_letivo_id',
        'disciplina',
        'disciplina_key',
        'carga_horaria_total_disciplina',
        'turma_id',
        'turma_nome',
        'avaliacao',
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
