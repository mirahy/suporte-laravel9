@extends('layouts.app')

@section('innerhead')
<link href="{{ asset('css/server.css?v-')}}{{getenv('VERSION_FILES')}}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">PHP info
                    <a type="button" name="retrun" class="btn btn-secondary botao-barra" href="/" title="Voltar">
                        <span class="span-icon-button"><i class="bi bi-box-arrow-left"></i></span>
                        Voltar
                    </a>
                </div>

                @if(isset($aviso))
                <div style="margin: 15px;" class="alert alert-success" role="alert">{{$aviso}}</div>
                @endif
                    @php
                    phpinfo()
                    @endphp
            </div>
        </div>
    </div>
</div>
@endsection
