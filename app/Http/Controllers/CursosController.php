<?php

namespace App\Http\Controllers;

use App\Curso;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CursosController extends Controller
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
        //
    }

    private function getValidationRules() {
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'nome'           => 'required',
            'faculdade_id'   => 'required',
            //'curso_key'      => 'required',
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

        $curso = new Curso();
        $curso->nome = $request->input('nome');
        $curso->faculdade_id = $request->input('faculdade_id');
        $curso->curso_key = $request->input('curso_key');
        $curso->auto_increment_ref = $request->input('auto_increment_ref');
        $curso->ativo = $request->input('ativo');
        $curso->save();
        return $curso;
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
        $curso = Curso::find($id);
        if (!$curso) 
            abort(404, 'Curso não encontrado');
        $validator = Validator::make($request->all(), $this->getValidationRules());
        if ($validator->fails()) 
            abort(403, 'Erro de Validação');
        $curso->nome = $request->input('nome');
        $curso->faculdade_id = $request->input('faculdade_id');
        $curso->curso_key = $request->input('curso_key');
        $curso->auto_increment_ref = $request->input('auto_increment_ref');
        $curso->ativo = $request->input('ativo');
        $curso->save();
        return $curso;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $curso = Curso::find($id);
        if (!$curso) 
            abort(404, 'Curso não encontrado');
        try{
            $curso->delete();
            return new Curso();
        }
        catch (Exception $e) {
            abort(400, $e->getMessage());
        }
    }
}
