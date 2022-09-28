<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <link rel="shortcut icon" href="/favicon.png" />

    <!-- Styles -->
    <link href="css/codigo-barras.css" rel="stylesheet">
    <link href="css/app.css" rel="stylesheet">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!--script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script-->

    <script type="text/javascript" src="js/browser-detect.js"></script>

    @yield('innerhead')

    <link href="css/style.css" rel="stylesheet">

    @inject('resources', 'App\Services\ResourcesService')
</head>
<body>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}" style="padding: 0px 15px;">
                    <img src="img/logo.png" alt="Salas Moodle" style="width: 75px;">
                </a>
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name') }}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    @if(isset($resources->permissao) && ($resources->permissao != 'INATIVO'))
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                                Solicitações <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                @if($resources->permissao == 'USUARIO' || $resources->permissao == 'ADMINISTRADOR')
                                <li>
                                    <a href="/salas/create/">
                                        Nova Solicitação de Sala
                                    </a>
                                </li>
                                @endif
                                @if($resources->permissao == 'ADMINISTRADOR')
                                <li>
                                    <a href="/lote-salas">
                                        Lote de Solicitações de Sala
                                    </a>
                                </li>
                                @endif
                                @if($resources->permissao == 'ADMINISTRADOR')
                                <li>
                                    <a href="/lote-salas-simplificados">
                                        Lote de Solicitações de Salas Simplificadas
                                    </a>
                                </li>
                                @endif
                                @if($resources->permissao == 'ADMINISTRADOR')
                                <li>
                                    <a href="/salas/">
                                        Lista de Solicitações
                                    </a>
                                </li>
                                @endif
                                @if($resources->permissao == 'ADMINISTRADOR')
                                <li>
                                    <a href="/salas-old/">
                                        Lista de Solicitações (Antiga)
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                                Universidade <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="/faculdades">
                                        Faculdades e Cursos
                                    </a>
                                </li>
                                <li>
                                    <a href="/periodo-letivos">
                                        Período Letivos
                                    </a>
                                </li>
                                <li>
                                    <a href="/periodo-letivos-categorias">
                                        Período Letivos - Categorias
                                    </a>
                                </li>
                                <li>
                                    <a href="/pl-disciplinas-academicos">
                                        Diciplinas e Estudantes
                                    </a>
                                </li>
                                <li>
                                    <a href="/unidade-organizacional">
                                        Unidades Organizacionais
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                    @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                                Administração <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="/usuarios">
                                        Usuários
                                    </a>
                                </li>
                                <li>
                                    <a href="/config">
                                        Configurações
                                    </a>
                                </li>
                                <li>
                                    <a href="/macro">
                                        Macros
                                    </a>
                                </li>
                                <li>
                                    <a href="/super-macro">
                                        Super Macros
                                    </a>
                                </li>
                                <li>
                                    <a href="/servidores-moodle">
                                        Servidores Moodle
                                    </a>
                                </li>
                                <li>
                                    <a href="/logs">
                                        Logs
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                    @if(isset($resources->permissao) && ($resources->permissao == 'ADMINISTRADOR'))
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                                Formulários Úteis <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="/formulario-insere-usuarios">
                                        Inserir Usuários Moodle
                                    </a>
                                </li>
                                <li>
                                    <a href="/formulario-insere-ad">
                                        Inserir Usuários AD
                                    </a>
                                </li>
                                <li>
                                    <a href="/formulario-altera-usuario">
                                        Alterar Senha Usuários AD
                                    </a>
                                </li>
                                <li>
                                    <a href="/formulario-pessoas-estatus-lotacao">
                                        Lista de Pessoas Por Lotação
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @guest
                        <li><a href="/login">Login</a></li>
                        <!--li><a href="{{ route('register') }}">Cadastrar</a></li-->
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu">
                                <li>
                                    <a href="/logout"
                                        onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="/logout" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div id="app">

        @yield('content')

    </div>

    <footer class="footer">
        <div class="container">
            <p>© EAD - UFGD</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="js/app.js"></script>
    @yield('postscripts')
    <script src="js/post-scrips.js"></script>
</body>
</html>
