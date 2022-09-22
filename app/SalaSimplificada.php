<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use App\SalaGenerica;

class SalaSimplificada extends Model
{

    protected $table = 'salas_simplificadas';


    protected $fillable = [
        'nome_sala',
        'periodo_letivo_id',
        'curso_id', 
        'sala_moodle_id',
        'link_moodle',
        'lote_id',
        'professor_id',
        'periodo_letivo_key',
        'disciplina_key',
        'turma_id',
        'turma_nome',
        'carga_horaria_total_disciplina',
        'avaliacao',
    ];

    protected $visible = [
        'id',
        'nome_sala',
        'periodo_letivo_id',
        'curso_id', 
        'sala_moodle_id',
        'link_moodle',
        'lote_id',
        'periodo_letivo_key',
        'disciplina_key',
        'turma_id',
        'turma_nome',
        'carga_horaria_total_disciplina',
        'avaliacao',
        'professor_id',
        'professor'
    ];

    protected $appends = array('professor');
    public function getProfessorAttribute($value) {
        return User::select('id', 'name', 'email')->where('id', $this->professor_id)->first();
    }

    public function lote()
    {
        return $this->belongsTo('App\LoteSalasSimplificado');
    }

    public function curso()
    {
        return $this->belongsTo('App\Curso','curso_id');
    }

    public function periodoLetivo()
    {
        return $this->belongsTo('App\PeriodoLetivo','periodo_letivo_id');
    }

    public function getSalaTemp($macroId) : SalaGenerica {
        $salaTemp = new SalaGenerica();
        //$sala->nome_professor = $request->input('nome_professor');
        $salaTemp->solicitante_id =                 $this->professor_id;
        $salaTemp->email =                          App::make('UsuarioService')->usuarioEmail($this->professor_id);
        $salaTemp->curso_id =                       $this->curso_id;
        $salaTemp->nome_sala =                      $this->nome_sala;
        //$salaTemp->modalidade =                     $this->;
        //$salaTemp->objetivo_sala =                  $this->;
        //$salaTemp->senha_aluno =                    $this->;
        //$salaTemp->observacao =                     $this->;
        //$salaTemp->estudantes =                     $this->;
        $salaTemp->periodo_letivo_id =              $this->periodo_letivo_id;
        $salaTemp->carga_horaria_total_disciplina = $this->carga_horaria_total_disciplina;
        $salaTemp->turma_nome =                     $this->turma_nome;
        $salaTemp->avaliacao =                      $this->avaliacao;
        $salaTemp->turma_id =                       $this->turma_id;
        $salaTemp->periodo_letivo_key =             $this->periodo_letivo_key;
        $salaTemp->disciplina_key =                 $this->disciplina_key;
        //$salaTemp->sala_moodle_id =                 $this->;
        $salaTemp->macro_id =                       $macroId;
        $salaTemp->lote_simplificado =              $this->lote;
        $salaTemp->geraSalaTemp();
        return $salaTemp;
    }
}
