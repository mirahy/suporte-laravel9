<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Buscador extends Model
{
    const BUSCADOR_NOME_SALA = "NOME_SALA";
    const BUSCADOR_NOME_PROFESSOR = "NOME_PROFESSOR";
    const BUSCADOR_SENHA_ALUNO = "SENHA_ALUNO";
    const BUSCADOR_SENHA_PROFESSOR = "SENHA_PROFESSOR";
    const BUSCADOR_EMAIL = "EMAIL";
    const BUSCADOR_FACULDADE = "FACULDADE";
    const BUSCADOR_CURSO = "CURSO";
    const BUSCADOR_PROVAO_ID = "PROVAO_ID";
    const BUSCADOR_TIMESTAMP_CORRENTE = "TIMESTAMP_CORRENTE";

    public static function getEntradasBuscadores() {
        return [
            self::BUSCADOR_NOME_SALA,
            self::BUSCADOR_NOME_PROFESSOR,
            self::BUSCADOR_SENHA_ALUNO,
            self::BUSCADOR_SENHA_PROFESSOR,
            self::BUSCADOR_EMAIL,
            self::BUSCADOR_FACULDADE,
            self::BUSCADOR_CURSO,
            self::BUSCADOR_PROVAO_ID,
            self::BUSCADOR_TIMESTAMP_CORRENTE,
        ];
    }
    
    protected $table = 'buscadores';

    protected $fillable = [
        'id',
        'chave',
        'entrada',
        'macro_id'
    ];
    protected $visible =  [
        'id',
        'chave',
        'entrada'
    ];

    /*protected $appends = array('macro');
    public function getMacroAttribute($value) {
        return Macro::find($this->macro_id);
    }
    public function setMacroAttribute($value) {
        $this->macro_id = $value->id;
    }*/
    
    /*
    * Muitos para um
    */
    
    public function macro()
    {
        return $this->belongsTo('App\Macro');
    }

}
