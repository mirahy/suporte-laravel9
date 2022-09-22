@extends('layouts.app')

@inject('resources', 'App\Services\ResourcesService')

@section('content')
<div class="content">
    <div class="title m-b-md">
        {{ config('app.name') }}
    </div>

    <div class="links">
        @if(isset($resources->permissao) && ($resources->permissao == 'USUARIO' || $resources->permissao == 'ADMINISTRADOR'))
            <a href="/salas/create/">Nova Solicitação de Sala</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/lote-salas">Lote de Solicitações de Sala</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/lote-salas-simplificados">Lotes Simplificados</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/salas/">Lista de Solicitações</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/salas/">Lista de Solicitações (Antiga)</a>
        @endif
        <br><br><br>
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        <a href="/faculdades/">Faculdades e Cursos</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/periodo-letivos/">Período Letivos</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/periodo-letivos-categorias/">Período Letivos - Categorias</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/pl-disciplinas-academicos/">Diciplinas e Estudantes</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/unidade-organizacional/">Unidades Organizacionais</a>
        @endif
        <br><br><br>
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        <a href="/usuarios/">Lista de Usuários</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/config/">Configurações</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/macro/">Macros</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/super-macro/">Super Macros</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/servidores-moodle/">Servidores Moodle</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/logs/">Logs</a>
        @endif
        <br><br><br>
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/formulario-insere-usuarios/">Inserir Usuários Moodle</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/formulario-insere-ad/">Inserir Usuários AD</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/formulario-altera-usuario/">Alterar Senha Usuários AD</a>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
            <a href="/formulario-pessoas-estatus-lotacao/">Lista de Pessoas Por Lotação</a>
        @endif
    </div>
</div>
@endsection
