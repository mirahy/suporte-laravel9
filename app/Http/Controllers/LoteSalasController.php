<?php

namespace App\Http\Controllers;

use App\Configuracoes;
use App\LoteSalas;
use App\Sala;
use App\Status;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class LoteSalasController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR);
    }

    public function executaExportacoes(Request $request, $loteId) {
        $loteSalas = LoteSalas::find($loteId);
        if (!$loteSalas)
            abort(400, 'Lote de Salas não Encontrado!');
        $loteSalas->is_salas_criadas = true;
        $loteSalas->save();
        $status_concluido = Status::where('chave', Status::STATUS_CONCLUIDO)->first();
        $ret = "<h4>Iniciando Exportações</h4><br>===========================================<br>";
        foreach ($loteSalas->salas as $sala) {
            $ret .= "<br><b>Exportando sala #".$sala->id."</b><br>";  
            if ($sala->status_id == $status_concluido->id){
                $ret .= '<span style="color: green">Esta Sala já está Concluída!</span>';
            }
            else {
                try {
                    $resposta = App::make('SalaService')->executarRestauracaoAutomatica($request, $sala->id, "cria", false);
                    if (App::make('SalaService')->posAutoRestore($sala, $resposta, $sala->macro->link_servidor_moodle . "/" . SalaController::SUFIXO_URL_SALAID)){
                        $sala->status_id = $status_concluido->id;
                        $sala->save();
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

    public function insereEstudantes(Request $request, $loteId) {
        $loteSalas = LoteSalas::find($loteId);
        if (!$loteSalas)
            abort(400, 'Lote de Salas não Encontrado!');
        $loteSalas->is_estudantes_inseridos = true;
        $loteSalas->save();
        $ret = "<h4>Iniciando Inserções</h4><br>===========================================<br>";
        foreach ($loteSalas->salas as $sala) {
            $ret .= "<br><b>Buscando e inserindo estudantes na sala #".$sala->id."</b><br>";  
            try {
                $estudantes = App::make('SalaService')->findEstudantesSigecad($request, $sala->disciplina_key, $sala->periodo_letivo_id, $sala->turma_id, $sala->turma_nome, $sala->solicitante_id);
                if ($estudantes == "[]") {
                    $ret .= '<span style="color: red">Não foram encontrados estudantes para esta sala!</span>';
                }
                else {
                    $sala->estudantes = $estudantes;
                    $sala->save();
                    try {
                        $resposta = App::make('SalaService')->executarRestauracaoAutomatica($request, $sala->id, "insere", false);
                        $ret .= $resposta;
                    }
                    catch (Exception $e) {
                        $ret .= '<span style="color: red">'.$e->getMessage()."</span>";
                    }
                }
            }
            catch (Exception $e) {
                $ret .= '<span style="color: red">'.$e->getMessage()."</span>";
            }
            $ret .= "<br><br>===========================================<br>";
        }
        return $ret."<h4>Inserções concluídas!</h4>";
    }

    public function updateMacro (Request $request) {
        $salaId = $request->input('sala_id');
        $macroId = $request->input('macro_id');
        if (!$salaId || !$macroId)
            abort(403, "Erro de Validação" );
        $sala = Sala::find ($salaId);
        if (!$sala)
            abort(403, "Sala não encontrada!" );
        $sala->macro_id = $macroId;
        $sala->save();
        return $sala->macro_id;
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
        return LoteSalas::all();
    }

    private function getValidationRules() {
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'descricao'             => 'required',
            'periodo_letivo_id'     => 'required',
            'faculdade_id'          => 'required',
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
        $loteSalas = new LoteSalas();
        $loteSalas->descricao = $request->input('descricao');
        $loteSalas->periodo_letivo_id = $request->input('periodo_letivo_id');
        $loteSalas->faculdade_id = $request->input('faculdade_id');
        $loteSalas->curso_id = $request->input('curso_id') == "0" ? null : $request->input('curso_id');
        $loteSalas->is_salas_criadas = false;
        $loteSalas->is_estudantes_inseridos = false;
//$loteSalas->id = 5;
        $loteSalas->save();
        return  [
            'id' => $loteSalas->id,
            'descricao' => $loteSalas->descricao,
            'periodo_letivo_id' => $loteSalas->periodo_letivo_id,
            'faculdade_id' => $loteSalas->faculdade_id,
            'curso_id' => $loteSalas->curso_id,
            'is_salas_criadas' => $loteSalas->is_salas_criadas,
            'is_estudantes_inseridos' => $loteSalas->is_estudantes_inseridos,
            'salas' => $this->geraSolicitacoes($request, $request->input('salas'), $loteSalas)
        ];
        return $loteSalas;
    }

    public function geraSolicitacoes(Request $request, $salasDados, $loteSalas) 
    {
        $status = Status::where('chave', Status::STATUS_PADRAO_INICIO)->first();
        $salas = [];
        foreach ($salasDados as $salaDados) {
            $salas[] = $this->criaSolicitacaoUna($request, $salaDados, $loteSalas, $status);
        }
        return $salas;
    }

    private function obtemSolicitanteId(Request $request, $salaDados) 
    {
        $solicitante = App::make('UsuarioService')->getInfosLdapServidorByDescription($salaDados['cpf_professor'], true);
        if ($solicitante)
            return $solicitante;
        return ['userId' => 0, 'email' => ''];
    }

    public function criaSolicitacaoUna(Request $request, $salaDados, $loteSalas, $status)
    {
         // validate
         /*$validator = Validator::make($request->all(), SalaController::getValidationRules(true));

         // process the login
         if ($validator->fails()) {
             abort(403, 'Erro de Validação');*/
        $sufixoNomeSala = SalaController::getSufixoNomeSala($salaDados['periodo_letivo_id']);

        $sala = new Sala();
        $solicitante = $this->obtemSolicitanteId($request, $salaDados);
        $sala->solicitante_id = $solicitante['userId'];
        $sala->email = $solicitante['email'];
        $sala->curso_id = $salaDados['curso'];
        $sala->nome_sala = $salaDados['nome_sala'] . ($sufixoNomeSala ? ' '.$sufixoNomeSala: '');
        $sala->modalidade = $salaDados['modalidade'];
        $sala->objetivo_sala = $salaDados['objetivo_sala'];
        $sala->senha_aluno = $salaDados['senha_aluno'];
        $sala->observacao = $salaDados['observacao'];
        $sala->status = $status;//Status::where('chave', Status::STATUS_PADRAO_INICIO)->first();
        $sala->periodo_letivo_id = $salaDados['periodo_letivo_id'];
        $sala->carga_horaria_total_disciplina = $salaDados['carga_horaria_total_disciplina'];
        $sala->avaliacao = $salaDados['avaliacao'];
        $sala->turma_nome = $salaDados['turma_nome'];
        $sala->turma_id = $salaDados['turma_id'];
        $sala->periodo_letivo_key = $salaDados['periodo_letivo_key'];
        //$sala->curso_key = $salaDados['curso_key'];
        $sala->disciplina_key = $salaDados['disciplina_key'];

        $macro = App::make('SuperMacroService')->getMacroEspecializada($request, $sala);
        $sala->macro_id = $macro->id;
        $sala->lote_salas_id = $loteSalas->id;

        $sala->estudantes = $salaDados['estudantes'] ?  $salaDados['estudantes'] : App::make('SalaService')->findEstudantesSigecad ($request, $sala->disciplina_key, $sala->periodo_letivo_id, $sala->turma_id, $sala->turma_nome, $sala->solicitante_id);
      
        $sala->save();
        //$sala->id = 0;
        return $sala;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loteSalas = LoteSalas::find($id);
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
        /*$loteSalas = LoteSalas::find($id);
        if (!$loteSalas)
            abort(400, 'Lote de Salas não Encontrado!');
        $validator = Validator::make($request->all(), $this->getValidationRules());
        if ($validator->fails()) 
            abort(403, 'Erro de Validação');

        $loteSalas->periodo_letivo_id = $request->input('periodo_letivo_id');
        $loteSalas->faculdade_id = $request->input('faculdade_id');
        $loteSalas->curso_id = $request->input('curso_id');
        $loteSalas->is_salas_criadas = $request->input('is_salas_criadas');
        $loteSalas->is_estudantes_inseridos = $request->input('is_estudantes_inserido');
        $loteSalas->save();
        return $loteSalas;*/
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ls = LoteSalas::find($id);
        if (!$ls) 
            abort(404, 'Lote de Salas não encontrado');
        try{
            $ls->delete();
            return new LoteSalas();
        }
        catch (Exception $e) {
            abort(400, $e->getMessage());
        }
    }
}
