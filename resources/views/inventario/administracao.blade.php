@extends('layouts.app-angular')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-14">
            <div class="panel panel-default">
                <div class="panel-heading">
                    @if($permissao == 'PRESIDENTE' || $permissao == 'ADMINISTRADOR')
                    Administração de Inventário
                    @else
                    Consultar Inventário
                    @endif
                </div>
                <app-administracao><i>Carregando...</i></app-administracao>
            </div>
        </div>
    </div>
</div>
@endsection
@section('mainscripts')
    <script type="text/javascript" src="js/angular/main.js"></script>
    <!--script type="text/javascript" src="js/mains/main-administracao.js"></script-->
@endsection
