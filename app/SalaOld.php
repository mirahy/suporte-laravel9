<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalaOld extends Model
{

    protected $table = 'salas_old';

    protected $fillable = [
        'status_id',
        'nome_professor',
        'email',
        'faculdade',
        'curso',
        'nome_sala',
        'modalidade',
        'objetivo_sala',
        'senha_aluno',
        'senha_professor',
        'observacao',
        'mensagem',
        'macro_id'
    ];

    protected $visible =  [
        'id',
        'nome_professor',
        'email',
        'faculdade',
        'curso',
        'nome_sala',
        'modalidade',
        'objetivo_sala',
        'senha_aluno',
        'senha_professor',
        'observacao',
        'status',
        'mensagem',
        'created_at'
    ];

    protected $appends = array('status');
    public function getStatusAttribute($value) {
        return Status::find($this->status_id);
    }
    public function setStatusAttribute($value) {
        $this->status_id = $value->id;
    }
    
    public function macro()
    {
        return $this->belongsTo('App\Macro');
    }

    
}
