@extends('layouts.app')

@section('innerhead')
<base href="/">
<link id="themeStyleSheet" rel="stylesheet" type="text/css" href="{{ asset('css/themes/nova-dark/theme.css?v-')}}{{getenv('VERSION_FILES')}}" />
@endsection

@section('content')
<div class="container">
    <app-root >
        <div class="loading">
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
            <div class="loading-bar"></div>
        </div>
    </app-root>
</div>
@endsection

@section('postscripts')
    <script type="text/javascript" src="{{ asset('js/angular/runtime.js?v-')}}{{getenv('VERSION_FILES')}}"></script>
    <script type="text/javascript" src="{{ asset('js/angular/polyfills.js?v-')}}{{getenv('VERSION_FILES')}}"></script>
    <script type="text/javascript" src="{{ asset('js/angular/styles.js?v-')}}{{getenv('VERSION_FILES')}}"></script>
    <script type="text/javascript" src="{{ asset('js/angular/vendor.js?v-')}}{{getenv('VERSION_FILES')}}"></script>
    <script type="text/javascript" src="{{ asset('js/angular/main.js?v-')}}{{getenv('VERSION_FILES')}}"></script>
    <link href="{{ asset('css/callendar.css?v-')}}{{getenv('VERSION_FILES')}}" rel="stylesheet">
@endsection

