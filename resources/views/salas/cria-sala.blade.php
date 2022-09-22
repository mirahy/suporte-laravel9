@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">Nova Solicitação de Criação de Sala no Moodle</div>
            <form class="form" action="/salas" method="POST" style="text-align: left; max-width: 500px; margin: 15px auto; padding: 0px 10px">
                <label class="style1">Nome completo: *</label><br>
                <input class="form-control" type="text" name="nome_professor" data-toggle="tooltip" data-placement="top" placeholder="Nome completo" value="{{ $sala->nome_professor }}" disabled  ><br>

                <label class="style1">E-mail: *</label><br>
                <input class="form-control" type="email" name="email" placeholder="Preferência institucional" value="{{ $sala->email }}" required size="60"><br>

                <label class="style1">Confirmar E-mail: *</label> 

                <input type="checkbox" name="verificacao" value="" required><br><br>

                <label class="style1">Faculdade: *</label><br>
                <input class="form-control" type="text" name="faculdade" placeholder="Ex.: FACET" required  size="60"><br>

                <label class="style1">Curso: *</label><br>
                <input class="form-control" type="text" name="curso" placeholder="Ex.: Sistemas de Informação" required  size="60"><br>

                <label class="style1">Nome da Disciplina: *</label><br>
                <input class="form-control" type="text" name="nome_sala" placeholder="Ex.: Algoritimos III" required  size="60"><br>

                <label class="style1">Modalidade da Disciplina: *</label><br>              
                <select class="form-control" name="modalidade" required>
                    <option hidden disabled selected value> -- Selecione -- </option>
                    <option value="presencial">Presencial</option>
                    <option value="semipresencial">Semipresencial</option>
                    <option value="distancia">A Distância</option>
                    <option value="RAE">RAE (Regime Acadêmico Emergencial)</option>
                </select>
                <br><br>

                <label class="style1">Chave de Inscrição para Estudantes acessarem a sala:</label><br>
                <input class="form-control" type="text" name="senha_aluno" placeholder="" size="60"><br>

                <label class="style1">Chave de Inscrição para o Professor acessar a sala: *</label><br>
                <input class="form-control" type="text" name="senha_professor" placeholder="" required size="60"><br>

                <label class="style1">Observações: </label><br>
                <textarea class="form-control" name="observacao" placeholder="Ex:. Utilizem o conteúdo da sala do ano passado: Link da sala"></textarea><br>


                <input class="btn btn-primary botao-barra" type="submit" name="Enviar">
            </form>
        </div>
    </div>
</div>
@endsection


