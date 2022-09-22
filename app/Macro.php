<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Macro extends Model
{
    protected $table = 'macros';

    protected $fillable = [
        'nome',
        'arquivo',
        'periodo_letivo_id',
        'link_servidor_moodle',
    ];

    protected $visible =  [
        'id',
        'nome',
        'arquivo',
        'periodo_letivo_id',
        'link_servidor_moodle',
        'buscadores'
    ];

    protected $appends = array('buscadores');
    public function getBuscadoresAttribute($value) {
        return $this->buscadors;
    }
    
     /*
    * Um para muitos
    */
    public function buscadors()
    { 
        return $this->hasMany(Buscador::class);
    }
    /*
    * Um para muitos
    */
    public function salas()
    { 
        return $this->hasMany(Sala::class);
    }

    public function periodoLetivo()
    { 
        return $this->belongsTo('App\PeriodoLetivo');
    }
}

