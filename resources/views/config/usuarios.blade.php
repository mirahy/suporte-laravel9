@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Usuários</div>
                <table class="table">
                    <!--caption>
                        <table>
                            <tr>
                                <td class="col-md-1"><label for="filtro" class="col-md-1 control-label">Buscar:</label></td>
                                <td class="col-md-8"><input id="filtro" type="text" class="form-control" name="filtro" autofocus></td>
                            </tr>
                        </table>
                    </caption-->
                    <colgroup>
                        <col width="50px"/>
                        <col width="100%"/>
                        <col width="170px"/>
                        <col width="135px"/>
                        <col width="56px"/>
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Login</th>
                            <th scope="col">Permissão</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $u)
                        <tr>
                            <td>{{$u->id}}</td>
                            <td>{{$u->name}}</td>
                            <td>{{$u->email}}</td>
                            <td>{{$u->permissao}}</td>
                            <td>
                                <!--button type="button" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-save"></span>
                                </button-->
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection