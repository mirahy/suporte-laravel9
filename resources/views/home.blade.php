@extends('layouts.app')

@inject('resources', 'App\Services\ResourcesService')

@section('innerhead')
<script src="{{ asset('js/util.js?v-')}}{{getenv('VERSION_FILES')}}"></script>
@endsection

@section('content')
    <div class="table-row">
        @if(isset($resources->permissao) && ($resources->permissao == 'USUARIO' || $resources->permissao == 'ADMINISTRADOR'))
        <button class="btn btn-secondary btn-dropmain" type="button" data-toggle="collapse" data-target="#solicitacoes" aria-expanded="true" aria-controls="multiCollapseExample2">
            Solicitações
            <i class="bi bi-caret-down-fill"></i>
        </button>
        <div class="collapse in multi-collapse" id="solicitacoes">
            <div class="card card-body">
                @if(isset($resources->permissao) && ($resources->permissao == 'USUARIO' || $resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/salas/create/', 'text' => 'Nova Solicitação de Sala', 'classIcon' => 'bi bi-file-earmark-plus', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/lote-salas', 'text' => 'Lote de Solicitações de Sala', 'classIcon' => 'bi bi-database-add', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/lote-salas-simplificados', 'text' => 'Lotes Simplificados', 'classIcon' => 'bi bi-database-add', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'USUARIO' || $resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/salas/', 'text' => 'Lista de Solicitações', 'classIcon' => 'bi bi-list-check', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/salas/', 'text' => 'Lista de Solicitações (Antiga)', 'classIcon' => 'bi bi-list-check', 'classLink' => '' ])
                @endif
            </div>
        </div>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        <button class="btn btn-secondary btn-dropmain" type="button" data-toggle="collapse" data-target="#universidade" aria-expanded="false" aria-controls="multiCollapseExample2">
            Universidade
            <i class="bi bi-caret-down-fill"></i>
        </button>
        <div class="collapse multi-collapse" id="universidade">
            <div class="card card-body">
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/faculdades/', 'text' => 'Faculdades e Cursos', 'classIcon' => 'bi bi-mortarboard', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/periodo-letivos/', 'text' => 'Período Letivos', 'classIcon' => 'bi bi-calendar-event', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/periodo-letivos-categorias/', 'text' => 'Período Letivos - Categorias', 'classIcon' => 'bi bi-calendar-week', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/pl-disciplinas-academicos/', 'text' => 'Diciplinas e Estudantes', 'classIcon' => 'bi bi-book-half', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/unidade-organizacional/', 'text' => 'Unidades Organizacionais', 'classIcon' => 'bi bi-node-plus', 'classLink' => '' ])
                @endif
            </div>
        </div>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        <button class="btn btn-secondary btn-dropmain" type="button" data-toggle="collapse" data-target="#administracao" aria-expanded="false" aria-controls="multiCollapseExample2">
            Administração
            <i class="bi bi-caret-down-fill"></i>
        </button>
        <div class="collapse multi-collapse" id="administracao">
            <div class="card card-body">
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/macro/', 'text' => 'Macros', 'classIcon' => 'bi bi-diagram-2', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/super-macro/', 'text' => 'Super Macros', 'classIcon' => 'bi bi-diagram-3', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/servidores-moodle/', 'text' => 'Servidores Moodle', 'classIcon' => 'bi bi-hdd-network', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/config/', 'text' => 'Configurações', 'classIcon' => 'bi bi-gear', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/server/', 'text' => 'PHP info', 'classIcon' => 'bi bi-filetype-php', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/logs/', 'text' => 'Logs', 'classIcon' => 'bi bi-list-check', 'classLink' => '' ])
                @endif
            </div>
        </div>
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        <button class="btn btn-secondary btn-dropmain" type="button" data-toggle="collapse" data-target="#gestaoUsuarios" aria-expanded="false" aria-controls="multiCollapseExample2">
            Gestão Usuários
            <i class="bi bi-caret-down-fill"></i>
        </button>
        <div class="collapse multi-collapse" id="gestaoUsuarios">
            <div class="card card-body">
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/formulario-insere-usuarios/', 'text' => 'Inserir Usuários Moodle', 'classIcon' => 'bi bi-person-add', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/formulario-insere-ad/', 'text' => 'Inserir Usuários AD', 'classIcon' => 'bi bi-person-add', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/formulario-altera-usuario/', 'text' => 'Alterar Senha Usuários AD', 'classIcon' => 'bi bi-person-lock', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/usuarios/', 'text' => 'Lista de Usuários', 'classIcon' => 'bi bi-person-lines-fill', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/roles/', 'text' => 'Perfis - Acessos', 'classIcon' => 'bi bi-shield-lock', 'classLink' => '' ])
                @endif
                @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                @include('templates.cards', ['link' => '/formulario-pessoas-estatus-lotacao/', 'text' => 'Lista de Pessoas Por Lotação', 'classIcon' => 'bi bi-person-down', 'classLink' => '' ])
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
