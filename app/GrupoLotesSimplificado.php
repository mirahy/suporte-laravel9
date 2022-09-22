<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GrupoLotesSimplificado extends Model
{
    protected $table = 'grupo_lotes_simplificados';
    
    protected $fillable = [
        'descricao',
        'auto_export_estudantes'
    ];

    protected $visible = [
        'id',
        'descricao',
        'auto_export_estudantes'
    ];

    
    public function lotesSimplificados()
    { 
        return $this->hasMany(LoteSalasSimplificado::class, 'grupo_id', 'id');
    }
}
