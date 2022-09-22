<?php

namespace App\Http\Controllers;

use App\Configuracoes;
use App\MacroSuperMacro;
use App\SalaSimplificada;
use App\SuperMacro;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class SuperMacroController extends Controller
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

    public function all()
    {
        return SuperMacro::all();
    }

    private function getValidationRules() {
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'descricao'                 => 'required',
            'macro_padrao_id'           => 'required'
        );
        return $rules;
    }

    private function getValidationRulesMSM() {
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'macro_id'                 => 'required',
            'super_macro_id'           => 'required',
            'ordem'                    => 'required',
            'campo'                    => 'required',
            //'operador'                 => 'required',
            'valor'                    => 'required',
        );
        return $rules;
    }

    public function getMacroEspecializada(Request $request, $sala)
    {
        $idSuperMacroPadrao = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_SUPER_MACRO_PADRAO)->first();
        if (!$idSuperMacroPadrao->valor)
            abort(400, 'Uma super macro padrão deve ser configurada');
        $superMacro = SuperMacro::find($idSuperMacroPadrao)->first();

        return $this->getMacroEspecializadaGenerica($superMacro, $sala);
    }

    public function getMacroEspecializadaSalaSimplificada(Request $request, SalaSimplificada $salaSimplificada)
    {
        $superMacro = $salaSimplificada->lote->superMacro;
        if (!$superMacro)
            return $this->getMacroEspecializada($request, $salaSimplificada);
        return $this->getMacroEspecializadaGenerica($superMacro, $salaSimplificada);
    }

    private function getMacroEspecializadaGenerica (SuperMacro $superMacro, $sala) {
        $msms = MacroSuperMacro::where('super_macro_id', $superMacro->id)->orderBy('ordem','ASC')->get();
        foreach ($msms as $msm) {
            switch($msm->campo) {
                case MacroSuperMacro::MSM_CAMPO_PERIODO_LETIVO:
                    if ($sala->periodoLetivo->nome == $msm->valor)
                        return $msm->macro;
                    break;
                case MacroSuperMacro::MSM_CAMPO_FACULDADE:
                    if ($sala->curso->faculdade->sigla == $msm->valor)
                        return $msm->macro;
                    break;
                case MacroSuperMacro::MSM_CAMPO_CURSO:
                    if ($sala->curso->nome == $msm->valor)
                        return $msm->macro;
                    break;
                case MacroSuperMacro::MSM_CAMPO_CARGA_HORARIA_DISCIPLINA:
                    if ($sala->carga_horaria_total_disciplina == $msm->valor)
                        return $msm->macro;
                    break;
                case MacroSuperMacro::MSM_CAMPO_TIPO_AVALIACAO:
                    if ($sala->avaliacao == $msm->valor)
                        return $msm->macro;
                    break;
            }
        }
        return $superMacro->macroPadrao;
    }

    public function allMSM(Request $request, $superMacroId)
    {
        $superMacro = SuperMacro::find($superMacroId);
        if (!$superMacro) 
            abort(404, 'SuperMacro não encontrada');
        return MacroSuperMacro::where('super_macro_id', $superMacro->id)->orderBy('ordem','ASC')->get();
    }
    public function newMSM(Request $request)
    {        
        $validator = Validator::make($request->all(), $this->getValidationRulesMSM());
        if ($validator->fails()) 
            abort(403, 'Erro de Validação');

        $msm = new MacroSuperMacro();
        $msm->macro_id = $request->input('macro_id');
        $msm->super_macro_id = $request->input('super_macro_id');
        $msm->ordem = MacroSuperMacro::getLastOrder($msm->super_macro_id)+1;
        $msm->campo = $request->input('campo');
        $msm->operador = $request->input('operador') ? $request->input('operador') : MacroSuperMacro::MSM_OPERADOR_DEFAULT;
        $msm->valor = $request->input('valor');
        $msm->save();
        return $msm;
    }
    public function delMSM(Request $request, $msmId)
    {
        $msm = MacroSuperMacro::find($msmId);
        if (!$msm) 
            abort(404, 'MacroSuperMacro não encontrada');
        try{
            $msm->delete();
            return new MacroSuperMacro();
        }
        catch (Exception $e) {
            abort(400, $e->getMessage());
        }
        
    }
    public function updateMSM(Request $request, $msmId)
    {
        $msm = MacroSuperMacro::find($msmId);
        if (!$msm) 
            abort(404, 'MacroSuperMacro não encontrada');
        $msm->macro_id = $request->input('macro_id');
        $msm->campo = $request->input('campo');
        $msm->operador = $request->input('operador') ? $request->input('operador') : MacroSuperMacro::MSM_OPERADOR_DEFAULT;
        $msm->valor = $request->input('valor');
        $msm->save();
        return $msm;
    }

    public function ordenaMSM(Request $request, $msmId1, $msmId2)
    {
        $msm1 = MacroSuperMacro::find($msmId1);
        if (!$msm1) 
            abort(404, 'MacroSuperMacro não encontrada ['.$msmId1.']');
        $msm2 = MacroSuperMacro::find($msmId2);
        if (!$msm2) 
            abort(404, 'MacroSuperMacro não encontrada ['.$msmId2.']');
        $ordTemp = $msm1->ordem;
        $msm1->ordem = $msm2->ordem;
        $msm2->ordem = $ordTemp;
        $msm1->save();
        $msm2->save();
        return $msm1->ordem;
    }

    public function getMsmCampos()
    {
        return MacroSuperMacro::MSM_CAMPOS;
    }

    public function getMsmOperadores()
    {
        return MacroSuperMacro::MSM_OPERADORES;
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

        $superMacro = new SuperMacro();
        $superMacro->descricao = $request->input('descricao');
        $superMacro->macro_padrao_id = $request->input('macro_padrao_id');
        $superMacro->save();
        return $superMacro;
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
        $superMacro = SuperMacro::find($id);
        if (!$superMacro) 
            abort(404, 'SuperMacro não encontrada');
        $validator = Validator::make($request->all(), $this->getValidationRules());
        if ($validator->fails()) 
            abort(403, 'Erro de Validação');
        $superMacro->descricao = $request->input('descricao');
        $superMacro->macro_padrao_id = $request->input('macro_padrao_id');
        $superMacro->save();
        return $superMacro;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $superMacro = SuperMacro::find($id);
        if (!$superMacro) 
            abort(404, 'SuperMacro não encontrada');
        try{
            $superMacro->delete();
            return new SuperMacro();
        }
        catch (Exception $e) {
            abort(400, $e->getMessage());
        }
    }
}
