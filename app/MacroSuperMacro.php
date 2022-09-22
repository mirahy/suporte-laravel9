<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MacroSuperMacro extends Model
{
    protected $table = 'macros_super_macros';

    const MSM_CAMPO_PERIODO_LETIVO = "PERIODO_LETIVO";
    const MSM_CAMPO_FACULDADE = "FACULDADE";
    const MSM_CAMPO_CURSO = "CURSO";
    const MSM_CAMPO_CARGA_HORARIA_DISCIPLINA = "CARGA_HORARIA_DISCIPLINA";
    const MSM_CAMPO_TIPO_AVALIACAO = "TIPO_AVALIACAO";

    const MSM_OPERADOR_DEFAULT = "=";

    const MSM_CAMPOS = [
        self::MSM_CAMPO_PERIODO_LETIVO,
        self::MSM_CAMPO_FACULDADE,
        self::MSM_CAMPO_CURSO,
        self::MSM_CAMPO_CARGA_HORARIA_DISCIPLINA,
        self::MSM_CAMPO_TIPO_AVALIACAO,
    ];

    const MSM_OPERADORES = [
        self::MSM_OPERADOR_DEFAULT,
    ];

    protected $fillable = [
        'macro_id',
        'super_macro_id',
        'ordem',
        'campo',
        'operador',
        'valor'
    ];

    protected $visible =  [
        'id',
        'macro_id',
        'super_macro_id',
        'ordem',
        'campo',
        'operador',
        'valor'
    ];
    
    public function macro()
    { 
        return $this->belongsTo('App\Macro');
    }

    public function superMacro()
    { 
        return $this->belongsTo('App\SuperMacro');
    }

    
    public static function getLastOrder($superMacroId)
    {
        $superMacro = SuperMacro::find($superMacroId);
        if (!$superMacro) 
            abort(404, 'SuperMacro não encontrada');
        $lastmsm = MacroSuperMacro::where('super_macro_id', $superMacro->id)->orderBy('ordem','DESC')->first();
        if ($lastmsm) 
            return $lastmsm->ordem;
        return 0;
    }
}
  