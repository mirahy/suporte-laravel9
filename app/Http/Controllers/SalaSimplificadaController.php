<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PeriodoLetivo;
use App\Sala;
use App\SalaSimplificada;
use App\User;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class SalaSimplificadaController extends Controller
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
        
    }

    
    private function getValidationRules() {
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'nome_sala'             => 'required',
            //'periodo_letivo_key'    => 'required',
            'curso_id'              => 'required',
            'disciplina_key'        => 'required',
            'lote_id'               => 'required',
            //'sala_moodle_id'        => 'required',
            'turma_id'              => 'required',
            //'turma_nome'            => 'required',
            //'cpf_professor'         => 'required',
        );
        return $rules;
    }

    public function insereEstudantes(Request $request, $sala) {
        if ( !($sala instanceof SalaSimplificada)) {
            $sala = SalaSimplificada::find($sala);
        }
        if (!$sala)
            return '<span style="color: red">Sala simplificada não encontrada! (#'.$sala.')</span>';
        $ret = "<br><b>Buscando e inserindo estudantes na sala #".$sala->id."</b><br>";  
        try {
            $estudantes = App::make('SalaService')->findEstudantesSigecad($request, $sala->disciplina_key, $sala->periodo_letivo_id, $sala->turma_id, $sala->turma_nome, $sala->professor_id);
            if ($estudantes == "[]") {
                $ret .= '<span style="color: red">Não foram encontrados estudantes para esta sala!</span>';
            }
            elseif(!$sala->sala_moodle_id && !$sala->lote->sala_provao_id) {
                $ret .= '<span style="color: red">Esta sala não possui uma associação com uma sala no moodle!</span>';
            }
            else {
                try {
                    if ($sala->sala_moodle_id) {
                        $resposta = $this->executarExportacaoEstudantes($request, $estudantes, $sala->lote->servidorMoodle->url, $sala->sala_moodle_id);
                        $ret .= $resposta;
                    }
                    else 
                        $ret .= '<span style="color: red">Esta sala não possui uma associação com uma sala no moodle!</span>';
                    if ($sala->lote->sala_provao_id) {
                        $ret .= "<br><b>Inserindo estudantes na sala provão '".$sala->lote->sala_provao_id."'</b><br>";  
                        $resposta = $this->executarExportacaoEstudantes($request, $estudantes, $sala->lote->servidorMoodle->url, $sala->lote->sala_provao_id, 'cadastra', false);
                        $ret .= $resposta;
                    }
                }
                catch (Exception $e) {
                    $ret .= '<span style="color: red">'.$e->getMessage()."</span>";
                }
            }
        }
        catch (Exception $e) {
            $ret .= '<span style="color: red">'.$e->getMessage()."</span>";
        }
        return $ret;
    }

    private function executarExportacaoEstudantes(Request $request, $estudantes, $linkServidorMoodle, $courseId, $modo = 'cadastra', $desativaEstudantes = true) {
        
        $categoriaId = null;

        $curlFile = null;
        $cURLConnection = curl_init();
        curl_setopt($cURLConnection, CURLOPT_URL, $linkServidorMoodle."/".SalaController::ARQUIVO_SCRIPT_RESTAURACAO_AUTOMATICA);
        curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cURLConnection, CURLOPT_POST, true);
        curl_setopt(
            $cURLConnection,
            CURLOPT_POSTFIELDS,
            array(
                'backupfile' => $curlFile,
                'modo' => $modo,
                'courseid' => $courseId,
                'categoryid' => $categoriaId,
                'senhapadrao' => null,
                'desativaEstudantes' => $desativaEstudantes,
                'usuarios' => $estudantes,
                'chaveWebservice' => base64_encode( env('CHAVE_WEBSERVICE_MOODLE', '') )
            ));
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        
        $resposta = curl_exec($cURLConnection);
        if (!$resposta)
            $resposta = '<span style="color: red">'.curl_error($cURLConnection)."</span><br>";
        curl_close($cURLConnection);
        return $resposta;
    }

    public function executarRestauracaoSala(Request $request, $salaId, $macroId, $courseImportId = null, $modo = 'cria', $comPos = true, $naoAbortar = false)
    {
        $salaSimplificada = null;
        if ($salaId instanceof SalaSimplificada)
            $salaSimplificada = $salaId;
        else
            $salaSimplificada = SalaSimplificada::find($salaId);
        if ($salaSimplificada == NULL) {
            if($naoAbortar)
                throw new Exception("Sala não Encontrada");
            else 
                abort(404, "Sala não Encontrada");
        }

        $linkServidorMoodle = $salaSimplificada->lote->servidorMoodle->url;
        if (!$linkServidorMoodle) {
            if($naoAbortar)
                throw new Exception("Link de Servidor Moodle não encontrado");
            else 
                abort(404, "Link de Servidor Moodle não encontrado");
        }

        $sala = $salaSimplificada->getSalaTemp($macroId);//$this->getMacro($request, $salaSimplificada)

        $categoriaId = $sala->getCategoriaId();
        if (!$categoriaId) {
            if($naoAbortar)
                throw new Exception("ID de Categoria não cadastrada para esta Sala");
            else 
                abort(400, "ID de Categoria não cadastrada para esta Sala");
        }

        $curlFile = null;
        if ($modo == 'full' || $modo == 'cria')
            $curlFile = App::make('MacroService')->makeCurlFile($sala);
        $cURLConnection = curl_init();
        curl_setopt($cURLConnection, CURLOPT_URL, $linkServidorMoodle."/".SalaController::ARQUIVO_SCRIPT_RESTAURACAO_AUTOMATICA);
        curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cURLConnection, CURLOPT_POST, true);
        curl_setopt(
            $cURLConnection,
            CURLOPT_POSTFIELDS,
            array(
                'backupfile' => $curlFile,
                'modo' => $modo,
                'categoryid' => $categoriaId,
                'desativaEstudantes' => true,
                'courseImportId' => $courseImportId,
                'usuarios' => $sala->getEstudantesComProfessor(),
                'chaveWebservice' => base64_encode( env('CHAVE_WEBSERVICE_MOODLE', '') )
            ));
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        
        $resposta = curl_exec($cURLConnection);
        if (!$resposta) {
            $resposta = '<span style="color: red">'.curl_error($cURLConnection)."</span><br>";
            $comPos = false;
        }
        curl_close($cURLConnection);

        if ($comPos)
            $this->posAutoRestore($salaSimplificada, $resposta, $linkServidorMoodle . "/" . SalaController::SUFIXO_URL_SALAID);

        return $resposta;
    }

    public function posAutoRestore(SalaSimplificada $salaSimplificada, $resposta, $prelink) {
        $courseId = 0;
        $posIn = strpos($resposta,SalaController::STRING_BUSCA_INIC_SALAID) + strlen(SalaController::STRING_BUSCA_INIC_SALAID);
        if ($posIn) {
            $posFim = strpos( substr($resposta,$posIn),SalaController::STRING_BUSCA_FIM_SALAID);
            if ($posFim)
                $courseId = (int) substr($resposta, $posIn, $posFim);
        }
        if ($courseId) {
            $salaSimplificada->link_moodle = $prelink . $courseId;
            $salaSimplificada->sala_moodle_id = $courseId;
            $salaSimplificada->save();
            return true;
        }
        return false;
    }
    
    public function getMacro(Request $request, $salaId)
    {
        $sala = null;
        if ($salaId instanceof SalaSimplificada)
            $sala = $salaId;
        else
            $sala = SalaSimplificada::find($salaId);
        if (!$sala) 
            abort(403, 'Sala simplificada não encontrada');
        $macro = App::make('SuperMacroService')->getMacroEspecializadaSalaSimplificada($request, $sala);
        return $macro ? $macro->id : "";
    }

    public function listLote(Request $request, $loteId)
    {
        return SalaSimplificada::where('lote_id', $loteId)->get();
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
        $professorId = null;
        $periodoLetivo = null;
        if ($request->has('periodo_letivo_nome') && $request->input('periodo_letivo_nome') ) 
            $periodoLetivo = PeriodoLetivo::where('nome', $request->input('periodo_letivo_nome'))->first();
        elseif($request->has('periodo_letivo_key') && $request->input('periodo_letivo_key')) 
            $periodoLetivo = PeriodoLetivo::where('id_sigecad', $request->input('periodo_letivo_key'))->first();
        if (!$periodoLetivo)
            abort(403, 'Erro de Validação [Período letivo inválido ou não informado]');
        if ($request->has('cpf_professor') && $request->input('cpf_professor') ) {
            $p = $this->obtemSolicitanteId($request, $request->input('cpf_professor'));
            if (isset($p['userId']) && $p['userId'])
                $professorId = $p['userId'];
        }
        else if ($request->has('professor_id') && $request->input('professor_id')) 
            $professorId = $request->input('professor_id');
        $sala = new SalaSimplificada();
        $sala->nome_sala = $request->input('nome_sala');
        $sala->professor_id = $professorId;
        $sala->curso_id = $request->input('curso_id');
        $sala->periodo_letivo_key = $request->input('periodo_letivo_key');
        $sala->disciplina_key = $request->input('disciplina_key');
        $sala->periodo_letivo_id = $periodoLetivo->id;
        $sala->turma_id = $request->input('turma_id');
        $sala->turma_nome = $request->input('turma_nome');
        $sala->carga_horaria_total_disciplina = $request->input('carga_horaria_total_disciplina');
        $sala->avaliacao = $request->input('avaliacao');
        $sala->sala_moodle_id = $request->input('sala_moodle_id');
        $sala->link_moodle = $request->input('link_moodle');
        $sala->lote_id = $request->input('lote_id');
        $sala->save();
        return $sala->id;
    }

    
    private function obtemSolicitanteId(Request $request, $cpf) 
    {
        $solicitante = App::make('UsuarioService')->getInfosLdapServidorByDescription($cpf, true);
        if ($solicitante)
            return $solicitante;
        return ['userId' => 0, 'email' => ''];
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
        $sala = SalaSimplificada::find($id);
        if (!$sala)
            abort(404, 'Sala Simplificada não encontrada!');
        $validator = Validator::make($request->all(), $this->getValidationRules());
        if ($validator->fails()) 
            abort(403, 'Erro de Validação', $validator->errors()->all());
        $professorId = null;
        $periodoLetivo = null;
        if ($request->has('periodo_letivo_nome') && $request->input('periodo_letivo_nome') ) 
            $periodoLetivo = PeriodoLetivo::where('nome', $request->input('periodo_letivo_nome'))->first();
        elseif($request->has('periodo_letivo_key') && $request->input('periodo_letivo_key')) 
            $periodoLetivo = PeriodoLetivo::where('id_sigecad', $request->input('periodo_letivo_key'))->first();
        if (!$periodoLetivo)
            abort(403, 'Erro de Validação [Período letivo inválido ou não informado]');
        if ($request->has('cpf_professor') && $request->input('cpf_professor') ) {
            $p = $this->obtemSolicitanteId($request, $request->input('cpf_professor'));
            if (isset($p['userId']) && $p['userId'])
                $professorId = $p['userId'];
        }
        else if ($request->has('professor_id') && $request->input('professor_id')) 
            $professorId = $request->input('professor_id');
        $sala->nome_sala = $request->input('nome_sala');
        $sala->professor_id = $professorId;
        $sala->curso_id = $request->input('curso_id');
        $sala->periodo_letivo_key = $request->input('periodo_letivo_key');
        $sala->disciplina_key = $request->input('disciplina_key');
        $sala->periodo_letivo_id = $periodoLetivo->id;
        $sala->turma_id = $request->input('turma_id');
        $sala->turma_nome = $request->input('turma_nome');
        $sala->carga_horaria_total_disciplina = $request->input('carga_horaria_total_disciplina');
        $sala->avaliacao = $request->input('avaliacao');
        $sala->sala_moodle_id = $request->input('sala_moodle_id');
        $sala->link_moodle = $request->input('link_moodle');
        $sala->lote_id = $request->input('lote_id');
        $sala->save();
        return $sala->id;
    }

    public function refreshSala(Request $request, $salaId)
    {
        $sala = SalaSimplificada::find($salaId);
        if (!$sala)
            abort(404, 'Sala Simplificada não encontrada!');
        
        $dadosSala = App::make('SigecadService')->chargeDisciplina($request, $sala->periodo_letivo_key, $sala->curso->curso_key, $sala->disciplina_key, $sala->turma_nome, false);
        
        $professorId = null;
        if ($dadosSala["cpf_professor"]) {
            $p = $this->obtemSolicitanteId($request, $dadosSala["cpf_professor"]);
            if (isset($p['userId']) && $p['userId'])
                $professorId = $p['userId'];
        }
        $sala->nome_sala = $dadosSala["nome_disciplina"];
        $sala->professor_id = $professorId;
        $sala->turma_id = $dadosSala["turma_id"];
        $sala->carga_horaria_total_disciplina = $dadosSala["carga_horaria_total_disciplina"];
        $sala->avaliacao = $dadosSala["avaliacao"];
        $sala->save();
        return $sala->id;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sala = SalaSimplificada::find($id);
        if (!$sala) 
            abort(404, 'Sala Simplificada não encontrada');
        try{
            $sala->delete();
            return new SalaSimplificada();
        }
        catch (Exception $e) {
            abort(400, $e->getMessage());
        }
    }
}
