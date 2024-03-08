<?php

namespace App\Http\Controllers;

use App\ServidorMoodle;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

# lista funções do serviço webservice

class ServidoresMoodleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR.','.User::PERMISSAO_SERVIDOR)->except(['links']);
    }

    public function all()
    {
        return ServidorMoodle::all();
    }


    public function links()
    {
        $sms = ServidorMoodle::where(['ativo' => true])->get(['url']);
        $links = [];
        foreach ($sms as $sm) {
            $links[] = $sm->url;
        }
        return $links;
    }

    public function downloadScript(Request $request){
        return Storage::disk('script')->download("auto-restore.php");
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

    public function formulariosIndex()
    {
        return view("layouts.app-angular");
    }

    public function exportarEstudantes(Request $request)
    {
        $msgValidacao = "";
        $estudantes = $request->input('estudantes');
        $servidorMoodle = $request->input('servidorMoodle');
        $courseId = $request->input('courseId');
        $senhaPadrao = $request->input('senhaPadrao');

        if (!$estudantes)
            $msgValidacao .= "\nEstudantes";
        if (!$servidorMoodle)
            $msgValidacao .= "\nLink do Servidor Moodle";
        if ($msgValidacao)
            abort(403, "<pre>Erro de Validação: Faltam dados".$msgValidacao."</pre>");

        $ret = "<pre>Estudantes: ";
        $ret .= $estudantes;
        $ret .= "\nServidor Moodle: ";
        $ret .= $servidorMoodle;
        $ret .= "\nCourseId: ";
        $ret .= $courseId;
        $ret .= "\nSenha Padrão: ";
        $ret .= $senhaPadrao;
        $ret .= "</pre>";

        return $this->executarExportacaoEstudantes($request, $estudantes, $servidorMoodle, $courseId ? $courseId : "", $senhaPadrao ? $senhaPadrao : "");

        return $ret;
    }

    private function executarExportacaoEstudantes(Request $request, $estudantes, $linkServidorMoodle, $courseId, $senhaPadrao, $modo = 'cadastra') {

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
                'senhapadrao' => $senhaPadrao,
                'usuarios' => $estudantes,
                'chaveWebservice' => base64_encode( env('CHAVE_WEBSERVICE_MOODLE', '') )
            ));
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

        $resposta = curl_exec($cURLConnection);
        curl_close($cURLConnection);
        return $resposta;
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
        if (!$request->input('url'))
            abort(400, "uma url é Requerida");
        $servidorMoodle = new ServidorMoodle();
        $servidorMoodle->nome = $request->input('nome');
        $servidorMoodle->url = $request->input('url');
        $servidorMoodle->ativo = $request->input('ativo');
        $servidorMoodle->save();
        return $servidorMoodle;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ServidorMoodle::find($id);
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
        $servidorMoodle = ServidorMoodle::find($id);
        if (!$servidorMoodle)
            abort(404, 'Servidor Moodle não encontrado');
        if (!$request->input('nome'))
            abort(400, "um nome é Requerido");
        if (!$request->input('url'))
            abort(400, "uma url é Requerida");
        $servidorMoodle->nome = $request->input('nome');
        $servidorMoodle->url = $request->input('url');
        $servidorMoodle->ativo = $request->input('ativo');
        $servidorMoodle->save();
        return $servidorMoodle;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $servidorMoodle = ServidorMoodle::find($id);
        if (!$servidorMoodle)
            abort(404, 'Servidor Moodle não encontrado');
        try{
            $servidorMoodle->delete();
            return new ServidorMoodle();
        }
        catch (Exception $e) {
            abort(404, $e->getMessage());
        }
    }

    public function getTokem($sala = null, $linkServidorMoodle){

        $link_backup_moodle = $sala->link_backup_moodle;
        $token = '';
        if($link_backup_moodle){

            if(str_contains($linkServidorMoodle,'moodle.ead') && str_contains($sala->link_backup_moodle,'moodle.ead'))
                $token = getenv('CHAVE_USER_WEBSERVICE_EAD');
            if(str_contains($linkServidorMoodle,'presencial.ead') && str_contains($sala->link_backup_moodle,'presencial.ead'))
                $token = getenv('CHAVE_USER_WEBSERVICE_PRESENCIAL');
            if(str_contains($linkServidorMoodle,'localhost:8082') && str_contains($sala->link_backup_moodle,'localhost'))
                $token = getenv('CHAVE_USER_WEBSERVICE_LOCAL');
            if(str_contains($linkServidorMoodle,'localhost:8084') && str_contains($sala->link_backup_moodle,'localhost'))
                $token = getenv('CHAVE_USER_WEBSERVICE_LOCAL2');
        }else{
            if(str_contains($linkServidorMoodle,'moodle.ead'))
                $token = getenv('CHAVE_USER_WEBSERVICE_EAD');
            if(str_contains($linkServidorMoodle,'presencial.ead'))
                $token = getenv('CHAVE_USER_WEBSERVICE_PRESENCIAL');
            if(str_contains($linkServidorMoodle,'localhost:8082'))
                $token = getenv('CHAVE_USER_WEBSERVICE_LOCAL');
            if(str_contains($linkServidorMoodle,'localhost:8084'))
                $token = getenv('CHAVE_USER_WEBSERVICE_LOCAL2');
        }
        if(!$token)
            App::make('MessagesService')->messagesHttp(404 , null, 'O link do conteúdo para restaurar: ' .
                                                        substr($link_backup_moodle,0, strpos($link_backup_moodle, 'br') ?
                                                        strpos($link_backup_moodle, 'br')+2 : 36).', é divergente do link onde irá gerar a sala: '.
                                                        $linkServidorMoodle );

        return $token;

    }

    public function getUser($login, $linkServidorMoodle, $token){
        //Obter id de usuário no moodle
        $userMoodle = Http::get($linkServidorMoodle . '/webservice/rest/server.php/', [
            'moodlewsrestformat'    => 'json',
            'wstoken'               => $token,
            'wsfunction'            => 'core_user_get_users_by_field',
            'field'                 => 'username',
            'values[0]'             => $login
        ]);
        if($userMoodle->successful() && !empty($userMoodle->json())){
            $user = $userMoodle->json();
        }elseif($userMoodle->failed() || empty($userMoodle->json()) ){
            App::make('MessagesService')->messagesHttp(404 , null, 'Usuário não encontrado no moodle');
        }

        return $user;
    }

    public function getIdUrl(Request $request, $url){
        $id ="";
        // verifica se existe espaço em branco e retira
        if(str_contains($url, ""))
            $id = trim($url);    
        // verifica se existe mais paremetros e retira
        if(str_contains($url, "&"))
            $id = substr($url, 0, stripos($url, "&"));
        //pega o id
        $id = substr($id, stripos($id, "id=") +3, strlen($id));
        
        return $id;
    }

    public function getCourse($id, $linkServidorMoodle, $token){
        //obter curso
        $course = Http::get($linkServidorMoodle . '/webservice/rest/server.php/', [
            'moodlewsrestformat'    => 'json',
            'wstoken'               => $token,
            'wsfunction'            => 'core_course_get_courses',
            'options[ids][0]'       => $id,
        ]);
        if($course->failed() || empty($course->json())){
            App::make('MessagesService')->messagesHttp(404, null, 'Sala do link informado não encontrada no moodle!');
        }
        return $course;
    }

    public function getCourseUser($id, $userId, $linkServidorMoodle, $token){
        //Obter perfis de usuário do curso por id
        $couserUser = Http::get($linkServidorMoodle . '/webservice/rest/server.php/', [
            'moodlewsrestformat'    => 'json',
            'wstoken'               => $token,
            'wsfunction'            => 'core_user_get_course_user_profiles',
            'userlist[0][userid]'   => $userId,
            'userlist[0][courseid]' => $id
        ]);
        if($couserUser->successful() && !empty($couserUser->json())){
            $couserUser = $couserUser->json();
        }elseif($couserUser->failed() || empty($couserUser->json())){
            App::make('MessagesService')->messagesHttp(404, null, 'Usuário não inscrito na sala!');
        }
        return $couserUser;
    }

    public function getUsersByCourse($id, $linkServidorMoodle, $token){
        //retorna tosdos os inscritos no curso do id passado
        $response = Http::get($linkServidorMoodle . '/webservice/rest/server.php/', [
                    'moodlewsrestformat'    => 'json',
                    'wstoken'               => $token,
                    'wsfunction'            => 'core_enrol_get_enrolled_users',
                    'courseid'              => $id
                ]);
                if($response->successful() && !empty($response->json())){
                    $response = $response->json();
                }elseif($response->failed() || empty($response->json())){
                    App::make('MessagesService')->messagesHttp(404, null, 'Não há inscritos na sala id '. $id .'!');
                }
        return $response;
    }

    public function createCourse($fullname, $shortname, $categoryid, $linkServidorMoodle, $token){
        //duplica curso referente ao id passado
        $response = Http::get($linkServidorMoodle . '/webservice/rest/server.php/', [
                    'moodlewsrestformat'    => 'json',
                    'wstoken'               => $token,
                    'wsfunction'            => 'core_course_create_courses',
                    'fullname'              => $fullname,
                    'shortname'             => $shortname,
                    'categoryid'            => $categoryid,
                ]);
                if($response->successful() && !empty($response->json())){
                    $response = $response->json();
                }elseif($response->failed() || empty($response->json())){
                    App::make('MessagesService')->messagesHttp(404, null, 'Erro ao duplicar o curso!');
                }
        return $response;
    }

    public function duplicateCoursebyNewCourse($id, $fullname, $shortname, $categoryid, $linkServidorMoodle, $token){
        //duplica curso referente ao id passado
        $response = Http::get($linkServidorMoodle . '/webservice/rest/server.php/', [
                    'moodlewsrestformat'    => 'json',
                    'wstoken'               => $token,
                    'wsfunction'            => 'core_course_duplicate_course',
                    'courseid'              => $id,
                    'fullname'              => $fullname,
                    'shortname'             => $shortname,
                    'categoryid'            => $categoryid,
                ]);
                if($response->successful() && !empty($response->json())){
                    $response = $response->json();
                }elseif($response->failed() || empty($response->json())){
                    App::make('MessagesService')->messagesHttp(404, null, 'Erro ao duplicar o curso!');
                }
        return $response;
    }

    public function importCoursebyNewCourse($importfrom, $importto, $linkServidorMoodle, $token){
        //duplica curso referente ao id passado
        $response = Http::get($linkServidorMoodle . '/webservice/rest/server.php/', [
                    'moodlewsrestformat'    => 'json',
                    'wstoken'               => $token,
                    'wsfunction'            => 'core_course_import_course',
                    'importfrom'            => $importfrom,
                    'importto'              => $importto,

                ]);
                if($response->successful() && !empty($response->json())){
                    $response = $response->json();
                }elseif($response->failed() || empty($response->json())){
                    App::make('MessagesService')->messagesHttp(404, null, 'Erro ao importar o curso!');
                }
        return $response;
    }


}
