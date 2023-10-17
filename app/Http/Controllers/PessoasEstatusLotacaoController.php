<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;

class PessoasEstatusLotacaoController extends Controller
{
    const NOME_ARQUIVO_PESSOAS_SAIDA = "pessoas.csv";

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('authhost');
        $this->middleware('permissao:' . User::PERMISSAO_ADMINISTRADOR );
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

    public function estatusList(Request $request)
    {
        return App::make('SigecadService')->getEstatusTipoPessoaList($request);
    }

    public function lotacoesList(Request $request)
    {
        $estatus = $request->has('estatus') ? $request->input('estatus') : null;
        if (!$estatus)
            abort(403, "Erro de validação! Parâmetros inválidos!");
        return App::make('SigecadService')->getLotacoesPessoaList($request, $estatus);
    }
    public function lotacoesFullList(Request $request)
    {
        return App::make('SigecadService')->getLotacoesPessoaFullList($request);
    }
    public function faculdadesList(Request $request)
    {
        $estatus = $request->has('estatus') ? $request->input('estatus') : null;
        if (!$estatus)
            abort(403, "Erro de validação! Parâmetros inválidos!");
        return App::make('SigecadService')->getFaculdadePessoaList($request, $estatus);
    }
    public function cursosFaculdadeList(Request $request)
    {
        $estatus = $request->has('estatus') ? $request->input('estatus') : null;
        $faculdade = $request->has('faculdade') ? $request->input('faculdade') : null;
        if (!$estatus || !$faculdade)
            abort(403, "Erro de validação! Parâmetros inválidos!");
        return App::make('SigecadService')->getCursosFaculdadePessoaList($request, $estatus, $faculdade);
    }

    public function getDadosAcademico(Request $request)
    {
        $estatus = $request->has('estatus') ? $request->input('estatus') : null;
        $curso = $request->has('curso') ? $request->input('curso') : null;
        $faculdade = $request->has('faculdade') ? $request->input('faculdade') : null;
        $tipo_pessoa = $request->has('tipo_pessoa') ? $request->input('tipo_pessoa') : null;

        if (!$estatus || !$tipo_pessoa)
            abort(403, "Erro de validação! Parâmetros inválidos!");

        $pessoas = App::make('SigecadService')->consultaPessoasVotacao($request, $estatus, $tipo_pessoa, $curso, $faculdade);
        return $this->geraArquivoPessoas($pessoas, $estatus);
    }

    public function getDadosFuncionarios(Request $request)
    {
        $estatus = $request->has('estatus') ? $request->input('estatus') : null;
        $lotacao = $request->has('lotacao') ? $request->input('lotacao') : null;
        $tipo_pessoa = $request->has('tipo_pessoa') ? $request->input('tipo_pessoa') : null;

        if (!$estatus || !$tipo_pessoa)
            abort(403, "Erro de validação! Parâmetros inválidos!");

        $pessoas = App::make('SigecadService')->consultaPessoasVotacao($request, $estatus, $tipo_pessoa, $lotacao);
        return $this->geraArquivoPessoas($pessoas, null);
    }

    private function geraArquivoPessoas($pessoas, $estatus = "") {
        $arquivoSaida = MacroController::CAMINHO_STORAGE_PADRAO."/processados/" . ($estatus ? str_replace(" ", "_",$this->tirarAcentos($estatus)).".csv" : self::NOME_ARQUIVO_PESSOAS_SAIDA);
        
        $texto = "";

        if ($pessoas)
        foreach ($pessoas as $pessoa) {
            if (!$texto) {
                $first = true;
                foreach ($pessoa as $k => $p) {
                    if (!$first)
                        $texto .= ",";
                    $texto .= $k;
                    $first = false;
                }
            }
            $texto .= "\n";
            $first = true;
            foreach ($pessoa as $k => $p) {
                if (!$first)
                    $texto .= ",";
                $texto .= $p;
                $first = false;
            }
        }
        
        $fp = fopen($arquivoSaida, "w");
        fwrite($fp, $texto);
        fclose($fp);
        
        return Response::download($arquivoSaida);
    }

    private function tirarAcentos($string){
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"),explode(" ","a A e E i I o O u U n N c C"),$string);
    }
}
