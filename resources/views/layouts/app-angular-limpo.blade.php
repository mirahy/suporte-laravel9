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
    <link href="{{ asset('css/app.css')}}" rel="stylesheet">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!--script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script-->

    <script type="text/javascript" src="{{ asset('js/browser-detect.js')}}"></script>

    <base href="/">
    <link id="themeStyleSheet" rel="stylesheet" type="text/css" href="{{ asset('css/themes/nova-dark/theme.css')}}" />

    <link href="{{ asset('css/style.css')}}" rel="stylesheet">

    @inject('resources', 'App\Services\ResourcesService')
</head>
<body>

            <app-root><i style="text-align: center; display: block">Carregando...</i></app-root>


    <!-- Scripts -->
    <script src="{{ asset('js/app.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/angular/runtime.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/angular/polyfills.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/angular/styles.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/angular/vendor.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/angular/main.js')}}"></script>
    <link href="{{ asset('css/callendar.css')}}" rel="stylesheet">
</body>
</html>
