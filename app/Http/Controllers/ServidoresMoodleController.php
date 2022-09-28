<?php

namespace App\Http\Controllers;

use App\ServidorMoodle;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServidoresMoodleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR);
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
}
