@extends('layouts.app')

@section('content')

    @if($link) 
        <div style="margin: 15px; text-align: center" class="alert alert-success" role="alert">        
            Sala Criada! Clique <a href="{{$link}}">AQUI</a> para acessá-la.
        </div>    
    @else
        <div style="margin: 15px; text-align: center" class="alert alert-success" role="alert">        
            Solicitação de Criação de Sala Enviada!
        </div>
    @endif
<div style="text-align: center">
    <a class="btn btn-primary" style="width: 92px;" href="/salas/create">Nova Sala</a>
    <a class="btn btn-secondary" style="width: 92px;" href="/">Início</a>
</div>

@endsection