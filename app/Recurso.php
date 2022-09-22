<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recurso extends Model
{

    protected $table = 'recursos';

    protected $fillable = [
        'nome',
        'descricao'
    ];

    protected $visible = [
        'id',
        'nome',
        'descricao',
        //'gestores'
    ];

    //protected $appends = array('gestores');
    public function getGestoresAttribute($value) {
        /*$gr = $this->gestoresRecursos;
        $ids = [];
        foreach ($gr as $g) {
            $ids[] = $g->id;
        }
        return $ids;*/
        return $this->gestoresRecursos;
    }

    /**
     * muitos para muitos
     */
    public function gestoresRecursos()
    {
        return $this->belongsToMany('App\User', 'gestores_recursos','recurso_id','user_id');
    }
}
