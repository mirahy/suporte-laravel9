<?php

namespace App\Http\Controllers;

use App\Recurso;
use App\User;
use Illuminate\Http\Request;

class RecursoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("layouts.app-angular");
    }

    public function listar()
    {
        return Recurso::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $recurso = new Recurso();
        $recurso->nome = $request->input('nome');
        $recurso->descricao = $request->input('descricao');
        $recurso->save();

        return $recurso;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recurso $recurso)
    {
        if ($recurso) {
            $recurso->nome = $request->input('nome');
            $recurso->descricao = $request->input('descricao');
            $recurso->save();
            return $recurso;
        }
    }

    public function getGestoresRecurso($recursoId) {
        $recurso = Recurso::find($recursoId);
        return $recurso->gestoresRecursos;
    }

    public function attachGestor($recursoId, $gestorId) {
        $recurso = Recurso::find($recursoId);
        $gestor = User::find($gestorId);
        if ($recurso != null && $gestor != null  && !$recurso->gestoresRecursos->contains($gestorId)) {
            $recurso->gestoresRecursos()->attach($gestorId);
            return "true";
        }
        abort(404, 'Informações não encontradas');
    }

    public function detachGestor($recursoId, $gestorId) {
        $recurso = Recurso::find($recursoId);
        $gestor = User::find($gestorId);
        if ($recurso != null && $gestor != null  && $recurso->gestoresRecursos->contains($gestorId)) {
            $recurso->gestoresRecursos()->detach($gestorId);
            return "true";
        }
        abort(404, 'Informações não encontradas');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
