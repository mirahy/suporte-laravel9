<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    const STATUS_ANALISE = "ANALISE";
    const STATUS_CONCLUIDO = "CONCLUIDO";
    const STATUS_REJEITADO = "REJEITADO";
    const STATUS_DEFERIDO = "DEFERIDO";
    const STATUS_INDEFERIDO = "INDEFERIDO";
    const STATUS_CANCELADO = "CANCELADO";
    const STATUS_PROCESSO = "PROCESSO";

    const STATUS_PADRAO_INICIO = self::STATUS_PROCESSO;
    const STATUS_PADRAO_SUCESSO = self::STATUS_CONCLUIDO;

    protected $table = 'status';
    
    protected $fillable = [
        'chave',
        'descricao',
        'cor'
    ];

    protected $visible = [
        'id',
        'chave',
        'descricao',
        'cor'
    ];
}
