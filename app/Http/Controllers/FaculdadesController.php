<?php

namespace App\Http\Controllers;

use App\Faculdade;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FaculdadesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR.','.User::PERMISSAO_SERVIDOR);
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

    public function all()
    {
        return Faculdade::all();
    }

    private function getValidationRules() {
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'sigla'          => 'required',
            'nome'           => 'required'
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
        $validator = Validator::make($request->all(), $this->getValidationRules());
        if ($validator->fails()) 
            abort(403, 'Erro de Validação');

        $faculdade = new Faculdade();
        $faculdade->sigla = $request->input('sigla');
        $faculdade->nome = $request->input('nome');
        $faculdade->auto_increment_ref = $request->input('auto_increment_ref');
        $faculdade->ativo = $request->input('ativo');
        $faculdade->save();
        return $faculdade;
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
        $faculdade = Faculdade::find($id);
        if (!$faculdade) 
            abort(404, 'Faculdade não encontrada');
        $validator = Validator::make($request->all(), $this->getValidationRules());
        if ($validator->fails()) 
            abort(403, 'Erro de Validação');
        $faculdade->sigla = $request->input('sigla');
        $faculdade->nome = $request->input('nome');
        $faculdade->auto_increment_ref = $request->input('auto_increment_ref');
        $faculdade->ativo = $request->input('ativo');
        $faculdade->save();
        return $faculdade;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $faculdade = Faculdade::find($id);
        if (!$faculdade) 
            abort(404, 'Faculdade não encontrada');
        try{
            $faculdade->delete();
            return new Faculdade();
        }
        catch (Exception $e) {
            abort(400, $e->getMessage());
        }
    }
}
