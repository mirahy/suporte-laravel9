<?php

namespace App\Http\Controllers;

use App\Configuracoes;
use App\PeriodoLetivo;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class PeriodoLetivosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissao:' . User::PERMISSAO_ADMINISTRADOR . ',' . User::PERMISSAO_SERVIDOR);
        $this->middleware('permissao:' . User::PERMISSAO_ADMINISTRADOR)->except(['all']);
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

    public function all(Request $request)
    {
        return PeriodoLetivo::all();
    }

    public function getPeriodoLetivoIdPadrao(Request $request)
    {
        $c = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_PERIODO_LETIVO_PADRAO)->first();
        if ($c)
            return $c->valor;
        return "";
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
        if (!$request->input('nome'))
            abort(400, "um nome é Requerido");
        $periodoLetivo = new PeriodoLetivo();
        $periodoLetivo->nome = $request->input('nome');
        $periodoLetivo->id_sigecad = $request->input('id_sigecad');
        $periodoLetivo->descricao = $request->input('descricao');
        $periodoLetivo->sufixo = $request->input('sufixo');
        $periodoLetivo->inicio_auto_increment = $request->input('inicio_auto_increment');
        $periodoLetivo->ativo = $request->has('ativo') ? $request->input('ativo') : false;
        $periodoLetivo->save();
        return $periodoLetivo;
    }

    public function getListaSigecad(Request $request)
    {
        return App::make('SigecadService')->getPeriodoLetivoList($request);
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
    public function update(Request $request, $id)
    {
        $periodoLetivo = PeriodoLetivo::find($id);
        if (!$periodoLetivo)
            abort(404, 'Periodo Letivo não encontrado');
        if (!$request->input('nome'))
            abort(400, "um nome é Requerido");
        $periodoLetivo->nome = $request->input('nome');
        $periodoLetivo->id_sigecad = $request->input('id_sigecad');
        $periodoLetivo->inicio_auto_increment = $request->input('inicio_auto_increment');
        $periodoLetivo->descricao = $request->input('descricao');
        $periodoLetivo->sufixo = $request->input('sufixo');
        $periodoLetivo->ativo = $request->has('ativo') ? $request->input('ativo') : false;
        $periodoLetivo->save();
        return $periodoLetivo;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $periodoLetivo = PeriodoLetivo::find($id);
        if (!$periodoLetivo)
            abort(404, 'Periodo Letivo não encontrado');
        try {
            $periodoLetivo->delete();
            return new PeriodoLetivo();
        } catch (Exception $e) {
            abort(404, $e->getMessage());
        }
    }
}
