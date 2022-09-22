<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';

    protected $fillable = [
        'title',
        'start',
        'end',
        'allDay',
        'maisDay',
        'backgroundColor',
        'recurso_id',
        'usuario_id',
        'status_id',
        'observacao',
        'justificativa'
    ];

    protected $visible =  [
        'id',
        'title',
        'start',
        'end',
        'allDay',
        'maisDay',
        'backgroundColor',
        'usuario_id',
        'status',
        'observacao',
        'justificativa',
        'created_at',
        'updated_at'
    ];

    protected $appends = array('status');
    public function getStatusAttribute($value) {
        return Status::find($this->status_id);
    }
    public function setStatusAttribute($value) {
        $this->status_id = $value->id;
    }

    /**
     * muitos para muitos
     */
    public function recurso()
    {
        return $this->hasOne('App\Recurso', 'id', 'recurso_id');
    }
}
