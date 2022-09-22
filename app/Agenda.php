<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'agenda';

    protected $fillable = [
        'title',
        'start',
        'end',
        'allDay',
        'maisDay',
        'backgroundColor'
    ];

    protected $visible =  [
        'id',
        'title',
        'start',
        'end',
        'allDay',
        'maisDay',
        'backgroundColor'
    ];
}
