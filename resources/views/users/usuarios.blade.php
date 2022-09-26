@extends('layouts.app-angular')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Usuários</div>
                <app-usuarios><i>Carregando...</i></app-usuarios>
            </div>
        </div>
    </div>
</div>
@endsection
@section('mainscripts')
    <!--script type="text/javascript" src="js/angular/main.js"></script-->
    <script type="text/javascript" src="js/mains/main-usuarios.js"></script>
@endsection