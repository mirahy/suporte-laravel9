@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Configurações</div>
                @if(isset($aviso))
                <div style="margin: 15px;" class="alert alert-success" role="alert">{{$aviso}}</div>
                @endif
                <form class="form" action="" method="POST" style="text-align: left; width: 500px; margin: 15px auto;">
                    <label class="style1">E-mail Suporte:</label><br>
                    <input class="form-control" type="text" name="email-suporte" placeholder="Ex.: suporte@gmail.com" value="{{ $emailSuporte }}" required>

                    <br><label class="style1" title="Deixe vazio para utilizar o sufixo do período letivo;
Digite 'NULL' para forçar deixar em branco.">
                        Sufixo padrão para os Nomes das Salas (?) </label><br>
                    <input class="form-control" title="Deixe vazio para utilizar o sufixo do período letivo;
Digite 'NULL' para forçar deixar em branco." type="text" name="sufixo-nome-sala" value="{{ $sufixoNomeSala }}">

                    <br><label class="style1">Período Letivo Padrão</label><br>
                    <select class="form-control" name="periodo-letivo-padrao" id="periodo-letivo-padrao">
                        <option value=""> -- nulo (obrigar seleção) -- </option>
                        @foreach ($periodoLetivos as $periodoLetivo)
                        <option value="{{$periodoLetivo->id}}" {{$idPeriodoLetivoPadrao == $periodoLetivo->id ? 'selected' : ''}}>{{$periodoLetivo->nome . ($periodoLetivo->descricao ? " (" . $periodoLetivo->descricao . ")" : "")}}</option>
                        @endforeach
                    </select>

                    <br><label class="style1">Nome do Arquivo de Saída</label><br>
                    <input class="form-control" type="text" name="arquivo-saida" placeholder="Ex. backup.tar.gz" value="{{ $arquivoSaida }}" required>

                    <br><label class="style1">Super Macro Padrão</label><br>
                    <select class="form-control" name="super-macro-padrao" id="super-macro-padrao">
                        @foreach ($superMacros as $superMacro)
                        <option value="{{$superMacro->id}}" {{$idSuperMacroPadrao == $superMacro->id ? 'selected' : ''}}>{{$superMacro->id. ' - ' . $superMacro->descricao}}</option>
                        @endforeach
                    </select>

                    <br><label class="style1">Filtro de Emails de Usuários Liberados</label><br>
                    <input class="form-control" type="text" name="regex-liberados" value="{{ $regexLiberados }}">

                    <br />
                    <button type="submit" class="btn btn-primary botao-barra" title="Enviar" name="Enviar">
                        <span class="span-icon-button"><i class="bi bi-send"></i></span>
                        Enviar
                    </button>
                    <a type="button" name="retrun" class="btn btn-secondary botao-barra" href="/">
                        <span class="span-icon-button"><i class="bi bi-box-arrow-left"></i></span>
                        Voltar
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection