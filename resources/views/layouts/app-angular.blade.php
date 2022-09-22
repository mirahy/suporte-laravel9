@extends('layouts.app')

@section('innerhead')
<base href="/">
<link id="themeStyleSheet" rel="stylesheet" type="text/css" href="css/themes/nova-dark/theme.css" />
@endsection

@section('content')
<div class="container">
    <app-root><i style="text-align: center; display: block">Carregando...</i></app-root>
</div>
@endsection

@section('postscripts')
    <script type="text/javascript" src="{{ asset('js/angular/runtime.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/angular/polyfills.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/angular/styles.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/angular/vendor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/angular/main.js') }}"></script>
    <link href="{{ asset('css/callendar.css') }}" rel="stylesheet">
@endsection

