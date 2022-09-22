@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">Visualizar Solicitação</div>
        <form class="form" style="text-align: left; width: 500px; margin: 15px auto;">
                <label class="style1">Nome completo: *</label><br>
                <input class="form-control" type="text" name="nome_professor" data-toggle="tooltip" data-placement="top" placeholder="Nome completo" value="{{ $sala->nome_professor }}" readonly size="60" ><br>

                <label class="style1">E-mail: *</label><br>
                <input class="form-control" type="email" name="email" placeholder="Preferência institucional" value="{{ $sala->email }}" readonly size="60"><br>

                <label class="style1">Faculdade: *</label><br>
                <input class="form-control" type="text" name="faculdade" placeholder="FACET, FAIND, FAEN..." value="{{ $sala->faculdade }}" readonly  size="60"><br>

                <label class="style1">Curso: *</label><br>
                <input class="form-control" type="text" name="curso" placeholder="Sistema de Informação, Matemática..." value="{{ $sala->curso }}" readonly  size="60"><br>

                <label class="style1">Nome da Disciplina: *</label><br>
                <input class="form-control" type="text" name="nome_sala" placeholder="Algoritimos III, Algebra Linear..." value="{{ $sala->nome_sala }}" readonly  size="60"><br>

                <label class="style1">Senha para Estudantes acessarem a sala:</label><br>
                <input class="form-control" type="text" name="senha_aluno" placeholder="" value="{{ $sala->senha_aluno }}" readonly size="60"><br>

                <label class="style1">Senha para acessar como professor: *</label><br>
                <input class="form-control" type="text" name="senha_professor" placeholder="" value="{{ $sala->senha_professor }}" readonly size="60"><br>

                <label class="style1">Observações:</label><br>
                <textarea class="form-control" name="observacao" placeholder="Ex:. Utilizem o conteúdo da sala do ano passado: Link da sala" readonly maxlength="255">{{ $sala->observacao }}</textarea><br>


                <input class="btn btn-primary botao-barra" type="submit" disabled name="Enviar">
                <a class="btn btn-secondary botao-barra" type="button" href="/salas">Voltar</a>
            </form>
        </div>
    </div>
</div>
@endsection
