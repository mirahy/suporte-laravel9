<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Adldap\Laravel\Facades\Adldap;
use App\Sala;
use Illuminate\Support\Facades\Validator;
use App\Configuracoes;
use App\Macro;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailUser;
use App\Modalidade;
use App\ObjetivoSala;
use App\PeriodoLetivo;
use App\PlDisciplinaAcademico;
use App\User;
use App\Status;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;


class SalaController extends Controller
{
    const ARQUIVO_SCRIPT_RESTAURACAO_AUTOMATICA = "auto-restore.php";
    const SUFIXO_URL_SALAID = "course/view.php?id=";
    const STRING_BUSCA_INIC_SALAID = "Curso Criado: [";
    const STRING_BUSCA_FIM_SALAID = "]";


    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('authdev:RubensMarcon');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR.','.User::PERMISSAO_SERVIDOR);
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR)->except(['create', 'store', 'success', 'preparaCreate', 'chargeDisciplina',
         'getModalidades', 'getObjetivosSalas', 'listar', 'getSalaMoodle',  'update', 'index']);


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
        $usuarioLogado = Auth::user();
        if($usuarioLogado->isAdmin()){
            return Sala::all();
        }elseif ($usuarioLogado->isUser()) {
            return Sala::where('solicitante_id', $usuarioLogado->id)->get();
        }

    }

    public function criar()
    {
        return view("salas.cria-sala");
    }

    public function visualizar($id)
    {
        $sala = Sala::findOrFail($id);
        return view("salas.visualiza-sala", ['sala' => $sala]);
    }

    public static function getValidationRules($full = false) {
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'nome_sala'         => 'required',
            'email'             => 'required|email',
            'curso'             => 'required',
            //'nome_professor'    => 'required',
            //'senha_professor'   => 'required'
        );
        return $rules;
    }

    public static function getSufixoNomeSala($plId) {
        $sufixoNomeSala = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_SUFIXO_NOME_SALA)->first()->valor;
        if ($sufixoNomeSala === "NULL")
            return "";
        if ($sufixoNomeSala)
            return $sufixoNomeSala;
        $pl = PeriodoLetivo::find($plId);
        if (!$pl) {
            //abort(400, "Período letivo não encontrado");
            return "";
        }
        return $pl->sufixo;
    }

    public function getSalaMoodle (Request $request, $id){

        $sala = new Sala();
        $sala->curso_id = $request->input('curso');
        $sala->periodo_letivo_id = $request->input('periodo_letivo_id');
        $sala->carga_horaria_total_disciplina = $request->input('carga_horaria_total_disciplina');
        $sala->link_backup_moodle = $request->input('link_backup_moodle');

        $macro = App::make('SuperMacroService')->getMacroEspecializada($request, $sala);
        $sala->macro_id = $macro->id;
        $linkServidorMoodle = $sala->macro->link_servidor_moodle;

        //Obter tokem referente ao link informado
        $token = App::make('ServidoresMoodleService')->getTokem($sala, $linkServidorMoodle );

        $login = substr($request->get('email'), 0, strripos($request->get('email'), "@"));

        try {
            //Obter id de usuário no moodle
            $user = App::make('ServidoresMoodleService')->getUser($login, $linkServidorMoodle, $token);

            //obter curso
            $course = App::make('ServidoresMoodleService')->getCourse($id, $linkServidorMoodle, $token);

            //Obter perfis de usuário do curso por id
            if(isset($user[0]['id']) && !empty($user[0]['id']))
                $couserUser = App::make('ServidoresMoodleService')->getCourseUser($id, $user[0]['id'], $linkServidorMoodle, $token);

            // verificar se solicitante é professor na sala do link informado
            $isTeacher = false;
            if(isset($couserUser[0]['roles']) && !empty($couserUser[0]['roles'])){
                $roles = $couserUser[0]['roles'];
                foreach($roles as $role){
                    if($role['roleid'] == 3 || $role['shortname'] == 'editingteacher' ){
                        $isTeacher = true;
                    }
                }
            }

            if(!$isTeacher){
                // se solicitante não é professor da sala, procura o professor
                $response = App::make('ServidoresMoodleService')->getUsersByCourse($id, $linkServidorMoodle, $token);

                if(is_array($response)){
                    foreach($response as $user){
                        if(isset($user['roles']) && !empty($user['roles'])){
                            unset($couserUser);
                            if($user['roles'][0]['roleid'] == 3 || $user['roles'][0]['shortname'] == 'editingteacher' ){
                                $couserUser = $user;
                                return $couserUser;
                            }
                        }
                    }
                    if(empty($couserUser)) {
                        App::make('MessagesService')->messagesHttp(404, null, 'Sala sem professor(es)!');
                    }
                }
            }else{
                if(array_key_exists(0, $couserUser)){
                    return $couserUser[0];
                }else{
                    return $couserUser;
                }
            }


            // // verificar se solicitante é professor na sala do link informado <- implementar tambem como informação na função gravar sala para exportação automática *******
            // $roles = $response[0]['roles'];
            // $isTeacher = false;
            // foreach($roles as $role){
            //     if($role['roleid'] == 3 || $role['shortname'] == 'editingteacher' ){
            //         $isTeacher = true;
            //     }
            // }
            // if(!$isTeacher){
            //     abort(400, 'Solicitante não é professor na sala do link informado!');
            // }

        } catch (\Throwable $th) {
            if(getenv('APP_DEBUG') == false){
                $configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
                App::make('MessagesService')->messagesHttp(500, null, 'Erro do Servidor na consulta de sala no moodle, entrar em contato com o suporte através do email: '. $configEmail->valor);
            }elseif(getenv('APP_DEBUG') == true){
                App::make('MessagesService')->messagesHttp(500, null, $th->getMessage());
            }else{
                App::make('MessagesService')->messagesHttp(500, null, "");
            }
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("layouts.app-angular");
    }

    public function createOld()
    {
        $sala = new Sala();
        $usuarioLogado = Auth::user();
        if ($usuarioLogado != null) {
            $sala->nome_professor = $usuarioLogado->name;
            $sala->email = App::make('UsuarioService')->usuarioEmail($usuarioLogado->id);
        }

        return view("salas.cria-sala", ['sala' => $sala]);
    }

    public function preparaCreate () {
        //$sala = (object) ['nome_professor'=> '', 'email' => ''];
        $sala = new Sala();
        $plId = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_PERIODO_LETIVO_PADRAO)->first();
        if ($plId)
            $sala->periodo_letivo_id = $plId->valor;
        $usuarioLogado = Auth::user();
        if ($usuarioLogado != null) {
            $sala->solicitante_id = $usuarioLogado->id;
            $sala->email = App::make('UsuarioService')->usuarioEmail($usuarioLogado->id);
        }

        //return json_encode($sala);
        if (!$this->crivoUsuariosAutorizados($sala))
            abort(401,"Usuário não autorizado à criar salas no moodle!");

        return $sala;
    }

    public function crivoUsuariosAutorizados($sala) {
        $regexLiberados = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_REGEX_EMAILS_LIBERADOS)->first();
        $pattern = "/".$regexLiberados->valor."/i";
        return preg_match($pattern, $sala->email);
    }

    public function statusSala(Request $request, $salaId,$status = NULL, $mensagem = NULL) {
        $sala = Sala::find($salaId);
        if ($sala == NULL)
            abort(404, "Sala não Encontrada");
        if ($status == NULL)
            $status = $request->input('status');
        if ($mensagem == NULL)
            $mensagem = $request->input('mensagem');

        $sala->status = Status::where('chave', $status)->first();
        if (!$sala->status)
            abort(404, "Status Inválido!");
        $sala->mensagem = $mensagem;
        $sala->save();
        $configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
        $configSeparadorEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_SEPARADOR_EMAIL)->first();
        if (config('app.debug')) {
            return $sala;
            return view('email.email', ['sala' => $sala, 'email' => ($configEmail == NULL ? "" : $configEmail->valor )]);
        }
        else {
            Mail::to(array_map('trim', explode($configSeparadorEmail, $sala->email)))
                ->cc($configEmail != null ? array_map('trim', explode($configSeparadorEmail, $configEmail->valor)) : "")
                ->send(new SendMailUser($sala));

            return $sala;
        }
        //return $this->index();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate
        $validator = Validator::make($request->all(), self::getValidationRules(true));

        // process the login
        if ($validator->fails()) {
            abort(403, 'Erro de Validação');
        } else {
            $sufixoNomeSala = self::getSufixoNomeSala($request->input('periodo_letivo_id'));

            $usuarioLogado = Auth::user();

            //$a = Usuario::create($request->all());
            $sala = new Sala();
            //$sala->nome_professor = $request->input('nome_professor');
            if ($usuarioLogado->isAdmin() && $request->input('solicitante_id'))
                $sala->solicitante_id = $request->input('solicitante_id');
            else
                $sala->solicitante_id = $usuarioLogado->id;
            $sala->email = $request->input('email');
            //$sala->faculdade = $request->input('faculdade');
            $sala->curso_id = $request->input('curso');
            $sala->nome_sala = $request->input('nome_sala') . ($sufixoNomeSala ? ' '.$sufixoNomeSala: '');
            $sala->modalidade = $request->input('modalidade');
            $sala->objetivo_sala = $request->input('objetivo_sala');
            $sala->senha_aluno = $request->input('senha_aluno');
            //$sala->senha_professor = $request->input('senha_professor');
            $sala->observacao = $request->input('observacao');
            $sala->link_backup_moodle = $request->input('link_backup_moodle');
            $sala->status = Status::where('chave', Status::STATUS_PADRAO_INICIO)->first();
            $sala->periodo_letivo_id = $request->input('periodo_letivo_id');
            $sala->carga_horaria_total_disciplina = $request->input('carga_horaria_total_disciplina');
            $sala->avaliacao = $request->input('avaliacao');
            $sala->turma_nome = $request->input('turma_nome');
            $sala->turma_id = $request->input('turma_id');
            $sala->periodo_letivo_key = $request->input('periodo_letivo_key');
            //$sala->curso_key = $request->input('curso_key');
            $sala->disciplina_key = $request->input('disciplina_key');

            $macro = App::make('SuperMacroService')->getMacroEspecializada($request, $sala);
            $sala->macro_id = $macro->id;

            $sala->estudantes = $request->input('estudantes') ?  $request->input('estudantes') : $this->findEstudantesSigecad ($request, $sala->disciplina_key, $sala->periodo_letivo_id, $sala->turma_id, $sala->turma_nome, $sala->solicitante_id);

            if ($this->crivoUsuariosAutorizados($sala))
                $sala->save();
            else
                abort(401,"Usuário não autorizado à criar salas no moodle!");

            $sala = $this->posCriaSala($request, $sala);

            $configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
            $configSeparadorEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_SEPARADOR_EMAIL)->first();
            if (config('app.debug')) {
                return ['sala' => $sala, 'email' => ($configEmail == NULL ? "" : $configEmail->valor ), 'redirect' => ''];
            }
            else {
                Mail::to(array_map('trim', explode($configSeparadorEmail, $sala->email)))
                    ->cc($configEmail != null ? array_map('trim', explode($configSeparadorEmail, $configEmail->valor)) : "")
                    ->send(new SendMailUser($sala));
                return ['sala' => $sala, 'email' => ($configEmail == NULL ? "" : $configEmail->valor ), 'redirect' => '/salas/success/'];
                //return Redirect::action('SalaController@success');
            }

            //return compact($a);
        }
    }

    public function posCriaSala(Request $request, $sala){
            // se tem observação e link, não cria sala no moodle, não havendo gera sala
        if (!$sala->observacao && !$sala->link_backup_moodle) {
            if ($this->executarRestauracaoAutomatica($request, $sala->id, 'cria', true, true)) {
                $sala = Sala::find($sala->id);
                $request->session()->put('link', $sala->mensagem);
                $sala->status = Status::where('chave', Status::STATUS_PADRAO_SUCESSO)->first();
                $sala->save();
            }
        }
        // se não tem observação e tem link, vai tentar gerar a sala no moodle
        if($sala->link_backup_moodle && !$sala->observacao){
            // pega link do moodle
            $linkServidorMoodle = $sala->macro->link_servidor_moodle;
            // pega id da sala para restauração
            $idSalaMoodle = App::make('ServidoresMoodleService')->getIdUrl($request, $sala->link_backup_moodle);
            // pega token referente a moodle específicado no link
            $token = App::make('ServidoresMoodleService')->getTokem($sala, $linkServidorMoodle );
            // pega login do ususário
            $login = substr($request->get('email'), 0, strripos($request->get('email'), "@"));
    
            try {
                //Obter id de usuário no moodle
                $user = App::make('ServidoresMoodleService')->getUser($login, $linkServidorMoodle, $token);
                //Obter perfis de usuário do curso por id
                $couserUser = App::make('ServidoresMoodleService')->getCourseUser($idSalaMoodle, $user[0]['id'], $linkServidorMoodle, $token);
                // verificar se solicitante é professor na sala do link informado
                $isTeacher = false;
                if(isset($couserUser[0]['roles']) && !empty($couserUser[0]['roles'])){
                    $roles = $couserUser[0]['roles'];
                    foreach($roles as $role){
                        if($role['roleid'] == 3 || $role['shortname'] == 'editingteacher' ){
                            $isTeacher = true;
                        }
                    }
                }
                // se solicitante é professor, cria sala no moodle
                if($isTeacher){
                    if ($this->executarRestauracaoAutomatica($request, $sala->id, 'cria', true, true, $idSalaMoodle)) {
                        $sala = Sala::find($sala->id);
                        $request->session()->put('link', $sala->mensagem);
                        $sala->status = Status::where('chave', Status::STATUS_PADRAO_SUCESSO)->first();
                        $sala->save();
                    }
                // se solicitante não é professor da sala, procura o professor e insere mensagem na observação da sala.
                }elseif(!$isTeacher) {
                    $response = App::make('ServidoresMoodleService')->getUsersByCourse($idSalaMoodle, $linkServidorMoodle, $token);
                    $teacher = "";
                    if(is_array($response)){
                        foreach($response as $user){
                            if(isset($user['roles']) && !empty($user['roles'])){
                                if($user['roles'][0]['roleid'] == 3 || $user['roles'][0]['shortname'] == 'editingteacher' ){
                                    if($teacher){
                                        $teacher = $teacher. ", ".$user["fullname"]."(".$user["email"].")";
                                    }else{
                                        $teacher = $user["fullname"] ."(".$user["email"].")";
                                    }
                                }
                            }
                        }
                    }

                    $configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
                    $sala = Sala::find($sala->id);
                    $sala->observacao = "Solicitante não é professor na sala do Moodle, por favor encaminhar autorização do(as)
                    Professores(as): ".$teacher." para o email do suporte: ". $configEmail->valor ." .";
                    $sala->save();
                }
    
            } catch (\Throwable $th) {
                if(getenv('APP_DEBUG') == false){
                    $configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
                    App::make('MessagesService')->messagesHttp(500, null, 'Erro do Servidor na consulta de sala no moodle, entrar em contato com o suporte através do email: '. $configEmail->valor);
                }elseif(getenv('APP_DEBUG') == true){
                    App::make('MessagesService')->messagesHttp(500, null, $th->getMessage());
                }else{
                    App::make('MessagesService')->messagesHttp(500, null, "");
                }
            }
            

        }

        return $sala;
    }

    public function exportarEstudantesMoodle(Request $request, $salaId){
        return $this->executarRestauracaoAutomatica($request, $salaId, 'insere', false);
    }

    public function executarRestauracaoAutomatica(Request $request, $salaId, $modo = 'cria', $comPos = true, $naoAbortar = false, $idSalaMoodle = null) {
        $sala = Sala::find($salaId);
        if ($sala == NULL) {
            if($naoAbortar)
                return false;
            else
                abort(404, "Sala não Encontrada");
        }
        if ($request->input('sala_moodle_id'))
            $sala->sala_moodle_id = $request->input('sala_moodle_id');
        if ($request->input('macro_id'))
            $sala->macro_id = $request->input('macro_id');
        $sala->save();

        $linkServidorMoodle = $sala->macro->link_servidor_moodle;
        if (!$linkServidorMoodle) {
            if($naoAbortar)
                return false;
            else
                abort(404, "Link de Servidor Moodle não encontrado");
        }
        $categoriaId = $sala->getCategoriaId();
        if (!$categoriaId) {
            if($naoAbortar)
                return false;
            else
                abort(400, "ID de Categoria não cadastrada para esta Sala");
        }

        // if ($request->has('courseImportId') || $request->input('link_backup_moodle')) {
        //     # code...
        // }else {
            $curlFile = null;
            if ($modo == 'full' || $modo == 'cria')
                $curlFile = App::make('MacroService')->makeCurlFile($salaId);
            $cURLConnection = curl_init();
            //curl_setopt($cURLConnection, CURLOPT_URL, "http://moodle/ccc.php");
            curl_setopt($cURLConnection, CURLOPT_URL, $linkServidorMoodle."/".self::ARQUIVO_SCRIPT_RESTAURACAO_AUTOMATICA);
            curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($cURLConnection, CURLOPT_POST, true);
            curl_setopt(
                $cURLConnection,
                CURLOPT_POSTFIELDS,
                array(
                    'backupfile' => $curlFile,
                    'modo' => $modo,
                    'courseid' => $sala->sala_moodle_id,
                    'pass_aluno' => $sala->senha_aluno,
                    'categoryid' => $categoriaId,
                    'courseImportId' => ($request->has('courseImportId') ? $request->get('courseImportId') : $idSalaMoodle),
                    'usuarios' => $sala->getEstudantesComProfessor(),
                    'chaveWebservice' => base64_encode( env('CHAVE_WEBSERVICE_MOODLE', '') )
                ));
            curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    
            $resposta = curl_exec($cURLConnection);
            curl_close($cURLConnection);
        // }


        if ($comPos)
            $this->posAutoRestore($sala, $resposta, $linkServidorMoodle . "/" . self::SUFIXO_URL_SALAID);

        return $resposta;
    }

    public function posAutoRestore(Sala $sala, $resposta, $prelink) {
        $courseId = 0;
        $posIn = strpos($resposta,self::STRING_BUSCA_INIC_SALAID) + strlen(self::STRING_BUSCA_INIC_SALAID);
        if ($posIn) {
            $posFim = strpos( substr($resposta,$posIn),self::STRING_BUSCA_FIM_SALAID);
            if ($posFim)
                $courseId = (int) substr($resposta, $posIn, $posFim);
        }
        if ($courseId) {
            $sala->mensagem = $prelink . $courseId;
            $sala->sala_moodle_id = $courseId;
            $sala->save();
            return true;
        }
        return false;
    }

    public function findEstudantes ($periodo_letivo_id, $curso_id, $disciplina) {
        $plc = PlDisciplinaAcademico::where(['periodo_letivo_id'=>$periodo_letivo_id, 'curso_id'=>$curso_id, 'disciplina'=>$disciplina])->first();
        if (!$plc)
            return "";
        $estAll = [];
        $plcs = PlDisciplinaAcademico::where(['periodo_letivo_id'=>$periodo_letivo_id, 'disciplina_key'=>$plc->disciplina_key])->get();
        foreach ($plcs as $p) {
            $estAll = array_merge($estAll ,json_decode($p->estudantes));
        }
        return count($estAll) ? json_encode($estAll) : "";
    }

    public function findEstudantesSigecad(Request $request, $codigoDisciplina, $periodoLetivoId, $turmaId, $turmaNome, $solicitanteId)
    {
        $pl = PeriodoLetivo::find($periodoLetivoId);
        if (!$pl)
            abort(400, "Período letivo não encontrado");
        $prof = User::find($solicitanteId);
        if (!$prof)
            abort(400, "Usuário não encontrado");

        try {
            $estudantes = App::make('SigecadService')->getAcademicosDisciplina($request, $codigoDisciplina, $pl->nome, $turmaId, $turmaNome, $prof);
            if ($estudantes)
                return json_encode( App::make('SigecadService')->formataPadraoSaidaEstudantes ($estudantes) );
        }
        catch (Exception $e) {
            //abort(500,$e->getMessage());
        }

        return "[]";
    }

    public function success(Request $request) {
        //$sala = Sala::find($salaId);
        $link = "";
        if ($request->session()->has('link')){
            $link = $request->session()->get('link');
            $request->session()->forget('link');
        }

        return view('salas.sucesso',['link' => $link]);
    }

    public function mensagem(Request $request, $salaId) {
        $sala = Sala::find($salaId);
        if ($sala == NULL)
            abort(404, "Sala não Encontrada");
        return $sala->mensagem;
    }

    public function chargeDisciplina(Request $request, $periodoLetivoKey, $codigoCurso, $codigoDisciplina, $salaTurma) {
        $usuarioLogado = Auth::user();
        $dadosSala = App::make('SigecadService')->chargeDisciplina($request, $periodoLetivoKey, $codigoCurso, $codigoDisciplina, $salaTurma, $usuarioLogado->email);
        return $dadosSala;
    }

    public function getModalidades(Request $request) {
        return Modalidade::all();
    }

    public function getObjetivosSalas(Request $request) {
        return ObjetivoSala::all();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sala = Sala::findOrFail($id);
        return view("salas.visualiza-sala", ['sala' => $sala]);
        //return $sala;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Sala  $sala
     * @return \Illuminate\Http\Response
     */
    public function edit(Sala $sala)
    {
        return view("salas.altera-sala",['sala' => $sala]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Sala  $sala
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sala $sala)
    {
        if ($sala->status->chave != Status::STATUS_ANALISE && $sala->status->chave != Status::STATUS_PROCESSO ) {
            //abort(403, 'Não pode alterar uma sala que já está finalizada!');
            return $this->updateSimplificado($request, $sala);
        }
        // validate
        $validator = Validator::make($request->all(), self::getValidationRules(true));

        // process the login
        if ($validator->fails()) {
            abort(403, 'Erro de Validação');
        } else {
            $usuarioLogado = Auth::user();
            if ($usuarioLogado->isAdmin() && $request->input('solicitante_id'))
                $sala->solicitante_id = $request->input('solicitante_id');
            //$sala->nome_professor = $request->input('nome_professor');
            $sala->email = $request->input('email');
            //$sala->faculdade = $request->input('faculdade');
            $sala->curso_id = $request->input('curso');
            $sala->nome_sala = $request->input('nome_sala');
            $sala->modalidade = $request->input('modalidade');
            $sala->objetivo_sala = $request->input('objetivo_sala');
            $sala->senha_aluno = $request->input('senha_aluno');
            //$sala->senha_professor = $request->input('senha_professor');
            $sala->observacao = $request->input('observacao');
            $sala->link_backup_moodle = $request->input('link_backup_moodle');
            $sala->estudantes = $request->input('estudantes');

            $sala->periodo_letivo_id = $request->input('periodo_letivo_id');
            $sala->carga_horaria_total_disciplina = $request->input('carga_horaria_total_disciplina');
            $sala->turma_nome = $request->input('turma_nome');
            $sala->avaliacao = $request->input('avaliacao');
            $sala->turma_id = $request->input('turma_id');
            $sala->periodo_letivo_key = $request->input('periodo_letivo_key');
            //$sala->curso_key = $request->input('curso_key');
            $sala->disciplina_key = $request->input('disciplina_key');
            $sala->sala_moodle_id = $request->input('sala_moodle_id');

            //$macro = App::make('SuperMacroService')->getMacroEspecializada($request, $sala);
            $sala->macro_id = $request->input('macro_id');
            $sala->save();

            /*$configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
            Mail::to($sala->email)
                ->cc($configEmail != null ? $configEmail->valor : "")
                ->send(new SendMailUser($sala));*/

            return redirect()->action('SalaController@index');
            //return compact($a);
        }
    }

    public function updateSimplificado(Request $request, Sala $sala)
    {
        $sala->sala_moodle_id = $request->input('sala_moodle_id');
        $sala->estudantes = $request->input('estudantes');
        $sala->macro_id = $request->input('macro_id');
        $sala->save();
    }

    public function sendEmail($id, Request $request,){
        $sala = Sala::find($id);
        $configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
        $configSeparadorEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_SEPARADOR_EMAIL)->first();
        if (config('app.debug')) {
            return ['sala' => $sala, 'email' => ($configEmail == NULL ? "" : $configEmail->valor ), 'redirect' => ''];
        }
        else {
            Mail::to(array_map('trim', explode($configSeparadorEmail, $sala->email)))
                ->cc($configEmail != null ? array_map('trim', explode($configSeparadorEmail, $configEmail->valor)) : "")
                ->send(new SendMailUser($sala));
            return ['sala' => $sala, 'email' => ($configEmail == NULL ? "" : $configEmail->valor ), 'redirect' => '/salas/success/'];
            //return Redirect::action('SalaController@success');
        }

        return false;
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
