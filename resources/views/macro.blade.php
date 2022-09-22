@extends('layouts.app')

@section('content')
<div class="content">
    <form action="/macro" method="post" enctype="multipart/form-data">
        <input type="file" name="arquivo" />
        <br />
        <input type="submit" value="Submeter" />
    </form>
    <pre style="text-align: left">
    @foreach ($arquivos as $arquivo)
        <a href="{{$arquivo->getPathName()}}">{{$arquivo->getFileName()}}</a>
    @endforeach
    </pre>
</div>
@endsection