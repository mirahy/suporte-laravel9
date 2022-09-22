@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">Alteração de Solicitação de Criação de Sala no Moodle</div>
        <form class="form" action="/salas/{{$sala->id}}" method="POST" style="text-align: left; width: 500px; margin: 15px auto;">
                <label class="style1">Nome completo: *</label><br>
                <input class="form-control" type="text" name="nome_professor" data-toggle="tooltip" data-placement="top" placeholder="Nome completo" value="{{ $sala->nome_professor }}" required autofocus size="60" ><br>

                <label class="style1">E-mail: *</label><br>
                <input class="form-control" type="email" name="email" placeholder="Preferência institucional" value="{{ $sala->email }}" required size="60"><br>

                <label class="style1">Faculdade: *</label><br>
                <input class="form-control" type="text" name="faculdade" placeholder="FACET, FAIND, FAEN..." value="{{ $sala->faculdade }}" required  size="60"><br>

                <label class="style1">Curso: *</label><br>
                <input class="form-control" type="text" name="curso" placeholder="Sistema de Informação, Matemática..." value="{{ $sala->curso }}" required  size="60"><br>

                <label class="style1">Nome da Disciplina: *</label><br>
                <input class="form-control" type="text" name="nome_sala" placeholder="Algoritimos III, Algebra Linear..." value="{{ $sala->nome_sala }}" required  size="60"><br>

                <label class="style1">Chave de Inscrição para Estudantes acessarem a sala:</label><br>
                <input class="form-control" type="text" name="senha_aluno" placeholder="" value="{{ $sala->senha_aluno }}" size="60"><br>

                <label class="style1">Chave de Inscrição para o Professor acessar a sala: *</label><br>
                <input class="form-control" type="text" name="senha_professor" placeholder="" value="{{ $sala->senha_professor }}" required size="60"><br>

                <label class="style1">Observações:</label><br>
                <textarea class="form-control" name="observacao" placeholder="Ex:. Utilizem o conteúdo da sala do ano passado: Link da sala">{{ $sala->observacao }}</textarea><br>


                <input class="btn btn-primary botao-barra" type="submit" name="Enviar">
                <a class="btn btn-secondary botao-barra" type="button" href="/salas">Voltar</a>
            </form>
        </div>
    </div>
</div>
@endsection
