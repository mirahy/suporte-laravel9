<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SuperMacro extends Model
{
    protected $table = 'super_macros';

    protected $fillable = [
        'descricao',
        //'periodo_letivo_id',
        'macro_padrao_id'
    ];

    protected $visible =  [
        'id',
        'descricao',
        //'periodo_letivo_id',
        'macro_padrao_id',
    ];
 
    public function macroPadrao() {
        return $this->belongsTo('App\Macro','macro_padrao_id');
    }
    /*public function periodoLetivo()
    { 
        return $this->belongsTo('App\PeriodoLetivo');
    }*/
}