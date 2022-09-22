@extends('layouts.app')

@section('content')
<div class="content" style="margin: auto; width: 90%;">
    <pre style="text-align: left">
        @foreach ($arquivos as $arquivo)
            <a href="/{{$arquivo->getPathName()}}">{{$arquivo->getFileName()}}</a>
        @endforeach
    </pre>
    <a type="button" class="btn btn-primary" href="/salas/">
        Voltar
    </a>
</div>
@endsection