@extends('layouts.app')

@inject('resources', 'App\Services\ResourcesService')

@section('content')
    <div class="table-row">
        @if(isset($resources->permissao) && ($resources->permissao == 'USUARIO' || $resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/salas/create/', 'text' => 'Nova Solicitação de Sala', 'classIcon' => 'bi bi-file-earmark-plus', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/lote-salas', 'text' => 'Lote de Solicitações de Sala', 'classIcon' => 'bi bi-database-add', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/lote-salas-simplificados', 'text' => 'Lotes Simplificados', 'classIcon' => 'bi bi-database-add', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/salas/', 'text' => 'Lista de Solicitações', 'classIcon' => 'bi bi-list-check', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/salas/', 'text' => 'Lista de Solicitações (Antiga)', 'classIcon' => 'bi bi-list-check', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/faculdades/', 'text' => 'Faculdades e Cursos', 'classIcon' => 'bi bi-mortarboard', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/periodo-letivos/', 'text' => 'Período Letivos', 'classIcon' => 'bi bi-calendar-event', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/periodo-letivos-categorias/', 'text' => 'Período Letivos - Categorias', 'classIcon' => 'bi bi-calendar-week', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/pl-disciplinas-academicos/', 'text' => 'Diciplinas e Estudantes', 'classIcon' => 'bi bi-book-half', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/unidade-organizacional/', 'text' => 'Unidades Organizacionais', 'classIcon' => 'bi bi-node-minus', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/macro/', 'text' => 'Macros', 'classIcon' => 'bi bi-diagram-2', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/super-macro/', 'text' => 'Super Macros', 'classIcon' => 'bi bi-diagram-3', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/servidores-moodle/', 'text' => 'Servidores Moodle', 'classIcon' => 'bi bi-hdd-network', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/config/', 'text' => 'Configurações', 'classIcon' => 'bi bi-gear', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/logs/', 'text' => 'Logs', 'classIcon' => 'bi bi-list-check', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/formulario-insere-usuarios/', 'text' => 'Inserir Usuários Moodle', 'classIcon' => 'bi bi-person-add', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/formulario-insere-ad/', 'text' => 'Inserir Usuários AD', 'classIcon' => 'bi bi-person-up', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/formulario-altera-usuario/', 'text' => 'Alterar Senha Usuários AD', 'classIcon' => 'bi bi-person-lock', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/usuarios/', 'text' => 'Lista de Usuários', 'classIcon' => 'bi bi-person-lines-fill', 'classLink' => '' ])
        @endif
        @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
        @include('layouts.cards', ['link' => '/formulario-pessoas-estatus-lotacao/', 'text' => 'Lista de Pessoas Por Lotação', 'classIcon' => 'bi bi-person-down', 'classLink' => '' ])
        @endif
    </div>
</div>
@endsection