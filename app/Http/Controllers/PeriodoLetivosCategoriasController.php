<?php

namespace App\Http\Controllers;

use App\PeriodoLetivoCategoria;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeriodoLetivosCategoriasController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR.','.User::PERMISSAO_USUARIO);
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR)->except(['all']);
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

    public function all(Request $request, $periodoLetivoId)
    {
        return PeriodoLetivoCategoria::where('periodo_letivo_id', $periodoLetivoId)->get();
    }

    private function getValidationRules() {
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'curso_id'          => 'required|numeric',
            'periodo_letivo_id' => 'required|numeric',
            'categoria_id'      => 'required|numeric'
        );
        return $rules;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->getValidationRules());
        if ($validator->fails()) 
            abort(403, 'Erro de Validação');

        $periodoLetivoCategoria = PeriodoLetivoCategoria::where(['curso_id' => $request->input('curso_id'), 'periodo_letivo_id' => $request->input('periodo_letivo_id')])->first();
        if (!$periodoLetivoCategoria)
            abort(404, '"Periodo Letivo - Categoria" não encontrado');

        $periodoLetivoCategoria->categoria_id = $request->input('categoria_id');
        $periodoLetivoCategoria->save();
        return $periodoLetivoCategoria->categoria_id;

        /*$periodoLetivoCategoria = new PeriodoLetivoCategoria();
        $periodoLetivoCategoria->curso_id = $request->input('curso_id');
        $periodoLetivoCategoria->periodo_letivo_id = $request->input('periodo_letivo_id');
        $periodoLetivoCategoria->categoria_id = $request->input('categoria_id');
        $periodoLetivoCategoria->save();
        return $periodoLetivoCategoria;*/
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
    {/*
        $periodoLetivoCategoria = PeriodoLetivoCategoria::find($id);
        if (!$periodoLetivoCategoria) 
            abort(404, '"Periodo Letivo - Categoria" não encontrado');*/
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {/*
        $periodoLetivoCategoria = PeriodoLetivoCategoria::find($id);
        if (!$periodoLetivoCategoria) 
            abort(404, '"Periodo Letivo - Categoria" não encontrado');
        try{
            $periodoLetivoCategoria->delete();
            return new PeriodoLetivoCategoria();
        }
        catch (Exception $e) {
            abort(400, $e->getMessage());
        }*/
    }
}
