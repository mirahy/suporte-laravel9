<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Loggador;
use App\Log;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class LogsController extends Controller
{
    const TIPO_ACAO_CREATE = "CREATE";
    const TIPO_ACAO_UPDATE = "UPDATE";
    const TIPO_ACAO_DELETE = "DELETE";

    const PATH_LOGS_EXPORTACOES_ESTUDANTES = "logs-exportacao-estudantes";
    const CAMINHO_STORAGE_LOGS_ESTUDANTES = "../storage/app/logs-exportacao-estudantes";

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR);
    }

    public function index()
    {
        return view("layouts.app-angular");
    }

    public function exportacaoEstudantes($arquivo = NULL) {
        if ($arquivo) {
            //$content = File::get('../storage/app/public/teste.html');
            $path = self::PATH_LOGS_EXPORTACOES_ESTUDANTES."/".$arquivo;
            if (Storage::exists($path))
                return view("print", ['content' => Storage::get($path)]);
            else
                abort(404, "Arquivo não Encontrado!");
        }
        else {
            $arrayRemessa = [];
            $files = File::files(self::CAMINHO_STORAGE_LOGS_ESTUDANTES);
            foreach($files as $path)
            {
                $arrayRemessa[] = $path->getFileName();
            }
            return $arrayRemessa;
        }
        
    }
    
    private function usuarioLogado() {
        return Auth::user();
    }
    public function geraLogCreate(Loggador $l) {
        $log = new Log();
        $log->usuario = $this->usuarioLogado();
        $log->tipo_acao = self::TIPO_ACAO_CREATE;
        $log->objeto = substr(get_class($l), strlen('App/') );
        $log->acao = "[".$l->getLogDescricao()."]";
        $log->save();
        return true;
    }
    public function geraLogUpdate(Loggador $a, Loggador $b) {
        $log = new Log();
        $log->usuario = $this->usuarioLogado();
        $log->tipo_acao = self::TIPO_ACAO_UPDATE;
        $log->objeto = substr(get_class($a), strlen('App/') );
        $log->acao = "[".$a->getLogDescricao(). ",\n" . $b->getLogDescricao()."]";
        $log->save();
        return true;
    }
    public function geraLogDelete(Loggador $l) {
        $log = new Log();
        $log->usuario = $this->usuarioLogado();
        $log->tipo_acao = self::TIPO_ACAO_DELETE;
        $log->objeto = substr(get_class($l), strlen('App/') );
        $log->acao = "[".$l->getLogDescricao()."]";
        $log->save();
        return true;
    }
}
