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
    <link href="{{ asset('css/codigo-barras.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!--script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script-->

    <script type="text/javascript" src="{{ asset('js/browser-detect.js') }}"></script>

    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/error.css') }}" rel="stylesheet">

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
                            <img src="{{ asset('img/logo.png') }}" alt="Salas Moodle" style="width: 75px;">
                        </a>
                        <a class="navbar-brand" href="{{ url('/') }}">
                            {{ config('app.name') }}
                        </a>
                    </div>

                    <div class="collapse navbar-collapse" id="app-navbar-collapse">
                        <!-- Left Side Of Navbar -->
                        
                    </div>
                </div>
            </nav>

    <div id="app">
            <div >
                @yield('content')
                @if( env('APP_ENV') == 'local' )
                    <br />
                    <h4>{{$exception->getMessage()}}</h4>
                    @if( env('APP_DEBUG') )
                        @if(isset($exception->xdebug_message))
                        <div class="xdebug">
                            <table>
                                {!! $exception->xdebug_message !!}
                            </table> 
                        </div>
                        @elseif(isset($exception->getPrevious()->xdebug_message))
                        <div class="xdebug">
                            <table>
                                {!! $exception->getPrevious()->xdebug_message !!}
                            </table> 
                        </div>
                        @else 
                            <pre style="margin: 10px; text-align: left;">
{!! $exception !!}
                            </pre>
                        @endif
                    @endif
                @endif
                <br />
                <button class="btn btn-danger" onclick="history.back()">Voltar</button>
            </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>© EAD - UFGD</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('postscripts')
</body>
</html>
