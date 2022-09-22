<?php
namespace App;

use App\Sala;

class SalaGenerica {

    public $solicitante_id;
    public $email;
    public $curso_id;
    public $nome_sala;
    public $periodo_letivo_id;
    public $carga_horaria_total_disciplina;
    public $turma_nome;
    public $avaliacao;
    public $turma_id;
    public $periodo_letivo_key;
    public $disciplina_key;
    public $macro_id;

    public $lote_simplificado;

    private $salaTemp;

    public function __construct()
    {
        $this->salaTemp = new Sala();
    }

    public function geraSalaTemp() {
        $this->salaTemp = new Sala();
        $this->salaTemp->solicitante_id =                 $this->solicitante_id;
        $this->salaTemp->email =                          $this->email;
        $this->salaTemp->curso_id =                       $this->curso_id;
        $this->salaTemp->nome_sala =                      $this->nome_sala;
        $this->salaTemp->periodo_letivo_id =              $this->periodo_letivo_id;
        $this->salaTemp->carga_horaria_total_disciplina = $this->carga_horaria_total_disciplina;
        $this->salaTemp->turma_nome =                     $this->turma_nome;
        $this->salaTemp->avaliacao =                      $this->avaliacao;
        $this->salaTemp->turma_id =                       $this->turma_id;
        $this->salaTemp->periodo_letivo_key =             $this->periodo_letivo_key;
        $this->salaTemp->disciplina_key =                 $this->disciplina_key;
        $this->salaTemp->macro_id =                       $this->macro_id;
    }

    public function getSalaTemp() {
        return $this->salaTemp;
    }

    public function macro()
    {
        return $this->salaTemp->macro;
    }

    public function solicitante()
    {
        return $this->salaTemp->solicitante;
    }

    public function curso()
    {
        return $this->salaTemp->curso;
    }

    public function periodoLetivo()
    {
        return $this->salaTemp->periodoLetivo;
    }
    
    /*public function loteSalas()
    { 
        return $this->salaTemp->loteSalas;
    }*/
    
    public function getEstudantesComProfessor()
    {
        return $this->salaTemp->getEstudantesComProfessor();
    }
    
    public function getCategoriaId() {
        return $this->salaTemp->getCategoriaId();
    }


}