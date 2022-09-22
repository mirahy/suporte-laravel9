<?php

namespace App\Http\Controllers;

use App\GrupoLotesSimplificado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class GrupoLotesSimplificadoController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR);
    }

    public function all()
    {
        return GrupoLotesSimplificado::all();
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
            'descricao'             => 'required',
            //'faculdade_id'          => 'required',
        );
        return $rules;
    }

    public function insereEstudantes(Request $request, $grupoId) {

        $grupo = null;
        if ($grupoId instanceof GrupoLotesSimplificado)
            $grupo = $grupoId;
        else
            $grupo = GrupoLotesSimplificado::find($grupoId);
        if (!$grupo)
            abort(400, 'Grupo de Lote de Salas Simplificado não Encontrado!');

        $ret = "<h3>Iniciando Inserções no grupo de lotes '#". $grupo->id ." - " . $grupo->descricao ."'</h3><br>===========================================<br>";
        foreach ($grupo->lotesSimplificados as $loteSalas) {
            $ret .= "<h4>Iniciando Inserções no lote '#". $loteSalas->id ." - " . $loteSalas->descricao."'</h4><br>===========================================<br>";
            foreach ($loteSalas->salasSimplificadas as $sala) {
                $ret .= App::make('SalaSimplificadaService')->insereEstudantes($request, $sala);
                $ret .= "<br><br>===========================================<br>";
            }
            $ret."<h4>Inserções concluídas no lote '".$loteSalas->descricao."'!</h4>";
        }
        return $ret."<h3>Inserções concluídas!</h3><br>===========================================";
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
            abort(403, 'Erro de Validação', $validator->errors()->all());
        $grupo = new GrupoLotesSimplificado();
        $grupo->descricao = $request->input('descricao');
        $grupo->auto_export_estudantes = $request->input('auto_export_estudantes');
        $grupo->save();
        return $grupo->id;
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
        $grupo = GrupoLotesSimplificado::find($id);
        if (!$grupo)
            abort(400, 'Grupo de Lote de Salas Simplificado não Encontrado!');
        $validator = Validator::make($request->all(), $this->getValidationRules());
        if ($validator->fails()) 
            abort(403, 'Erro de Validação', $validator->errors()->all());
        $grupo->descricao = $request->input('descricao');
        $grupo->auto_export_estudantes = $request->input('auto_export_estudantes');
        $grupo->save();
        return $grupo;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $g = GrupoLotesSimplificado::find($id);
        if (!$g) 
            abort(404, 'Lote de Salas Simplificado não encontrado');
        try{
            $g->delete();
            return new GrupoLotesSimplificado();
        }
        catch (Exception $e) {
            abort(400, $e->getMessage());
        }
    }
}
