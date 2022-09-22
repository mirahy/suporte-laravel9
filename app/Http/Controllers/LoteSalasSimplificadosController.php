<?php

namespace App\Http\Controllers;

use App\Configuracoes;
use App\LoteSalas;
use App\LoteSalasSimplificado;
use App\Sala;
use App\Status;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class LoteSalasSimplificadosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR);
    }

    public function insereEstudantes(Request $request, $loteId) {
        $loteSalas = LoteSalasSimplificado::find($loteId);
        if (!$loteSalas)
            abort(400, 'Lote de Salas Simplificado não Encontrado!');
        //$loteSalas->is_estudantes_inseridos = true;
        //$loteSalas->save();
        $ret = "<h4>Iniciando Inserções</h4><br>===========================================<br>";
        foreach ($loteSalas->salasSimplificadas as $sala) {
            $ret .= App::make('SalaSimplificadaService')->insereEstudantes($request, $sala);
            $ret .= "<br><br>===========================================<br>";
        }
        return $ret."<h4>Inserções concluídas!</h4>";
    }

    public function executaExportacoes(Request $request, $loteId) {
        $loteSalas = LoteSalasSimplificado::find($loteId);
        if (!$loteSalas)
            abort(400, 'Lote de Salas não Encontrado!');
        $ret = "<h4>Iniciando Exportações</h4><br>===========================================<br>";
        foreach ($loteSalas->salasSimplificadas as $sala) {
            $ret .= "<br><b>Exportando sala #".$sala->id."</b><br>";  
            if ($sala->sala_moodle_id){
                $ret .= '<span style="color: green">Esta Sala já está associada à uma sala criada previamente!</span>';
            }
            else {
                try {
                    $macroId = App::make('SalaSimplificadaService')->getMacro($request, $sala);
                    $resposta = App::make('SalaSimplificadaService')->executarRestauracaoSala($request, $sala, $macroId, null, "cria", false, true);
                    if (App::make('SalaSimplificadaService')->posAutoRestore($sala, $resposta, $sala->lote->servidorMoodle->url . "/" . SalaController::SUFIXO_URL_SALAID)){
                        $ret .= $resposta;
                    }
                    else
                        $ret .= '<span style="color: red">'.$resposta."</span>";
                }
                catch (Exception $e) {
                    $ret .= '<span style="color: red">'.$e->getMessage()."</span>";
                }
            }
            $ret .= "<br><br>===========================================<br>";
        }
        return $ret."<h4>Exportações concluídas!</h4>";
    }

    public function rota(Request $request)
    {
        //return $request->input('test');
        return "test";
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

    public function all($grupoId)
    {
        return LoteSalasSimplificado::where(['grupo_id' => $grupoId])->get();
    }

    private function getValidationRules() {
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'descricao'             => 'required',
            'grupo_id'              => 'required',
            'servidor_moodle_id'     => 'required',
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
            abort(403, 'Erro de Validação', $validator->errors()->all());
        $loteSalas = new LoteSalasSimplificado();
        $loteSalas->grupo_id = $request->input('grupo_id');
        $loteSalas->descricao = $request->input('descricao');
        $loteSalas->sala_provao_id = $request->input('sala_provao_id');
        $loteSalas->servidor_moodle_id = $request->input('servidor_moodle_id');
        $loteSalas->super_macro_id = $request->input('super_macro_id');
        $loteSalas->sufixo = $request->input('sufixo');
        $loteSalas->save();
        return $loteSalas->id;
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loteSalas = LoteSalasSimplificado::find($id);
        if (!$loteSalas)
            abort(400, "Lote de salas não encontrado!");
        return $loteSalas->salas;
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
        $loteSalas = LoteSalasSimplificado::find($id);
        if (!$loteSalas)
            abort(400, 'Lote de Salas Simplificado não Encontrado!');
        $validator = Validator::make($request->all(), $this->getValidationRules());
        if ($validator->fails()) 
            abort(403, 'Erro de Validação');

        //$loteSalas->grupo_id = $request->input('grupo_id');
        $loteSalas->descricao = $request->input('descricao');
        $loteSalas->sala_provao_id = $request->input('sala_provao_id');
        $loteSalas->servidor_moodle_id = $request->input('servidor_moodle_id');
        $loteSalas->super_macro_id = $request->input('super_macro_id');
        $loteSalas->sufixo = $request->input('sufixo');
        $loteSalas->save();
        return $loteSalas;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ls = LoteSalasSimplificado::find($id);
        if (!$ls) 
            abort(404, 'Lote de Salas Simplificado não encontrado');
        try{
            $ls->delete();
            return new LoteSalasSimplificado();
        }
        catch (Exception $e) {
            abort(400, $e->getMessage());
        }
    }
}
