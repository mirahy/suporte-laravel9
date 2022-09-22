<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Adldap\Laravel\Facades\Adldap;
use App\SalaOld;
use Illuminate\Support\Facades\Validator;
use App\Configuracoes;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailUser;
use App\Modalidade;
use App\ObjetivoSala;
use App\User;
use App\Status;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;

class SalasOldController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('authdev:RubensMarcon');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR.','.User::PERMISSAO_USUARIO);
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR)->except(['create', 'store', 'success']);
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
        return SalaOld::all();
    }

    public function criar()
    {
        return view("salas.cria-sala-old");
    }

    public function visualizar($id)
    {           
        $sala = SalaOld::findOrFail($id);
        return view("salas.visualiza-sala", ['sala' => $sala]);
    }

    private function getValidationRules($full = false) {
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'nome_sala'         => 'required',
            'email'             => 'required|email',
            'nome_professor'    => 'required',
            'senha_professor'   => 'required'
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
        $sala = new SalaOld();
        $usuarioLogado = Auth::user();
        if ($usuarioLogado != null) {
            $sala->nome_professor = $usuarioLogado->name;
            try {
                $usuarioLdap = Adldap::search()->users()->find($usuarioLogado->email);
                if ($usuarioLdap != NULL && isset ($usuarioLdap->getAttributes()["mail"]))
                    $sala->email = strtolower ($usuarioLdap->getAttributes()["mail"][0]);
            } catch (Exception $e) {
                
            }
        }
        
        return view("salas.cria-sala-old", ['sala' => $sala, 'modalidades' => Modalidade::all(), 'objetivos' => ObjetivoSala::all()]);
    }

    public function statusSala(Request $request, $salaId,$status = NULL, $mensagem = NULL) {
        $sala = SalaOld::find($salaId);
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
        $validator = Validator::make($request->all(), $this->getValidationRules(true));

        // process the login
        if ($validator->fails()) {
            abort(403, 'Erro de Validação');
        } else {
            $sufixoNomeSala = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_SUFIXO_NOME_SALA)->first()->valor;

            //$a = Usuario::create($request->all());
            $sala = new SalaOld();
            $sala->nome_professor = $request->input('nome_professor');
            $sala->email = $request->input('email');
            $sala->faculdade = $request->input('faculdade');
            $sala->curso = $request->input('curso');
            $sala->nome_sala = $request->input('nome_sala') . ($sufixoNomeSala ? ' '.$sufixoNomeSala: '');
            $sala->modalidade = $request->input('modalidade');
            $sala->objetivo_sala = $request->input('objetivo_sala');
            $sala->senha_aluno = $request->input('senha_aluno');
            $sala->senha_professor = $request->input('senha_professor');
            $sala->observacao = $request->input('observacao');
            $sala->status = Status::where('chave', Status::STATUS_PADRAO_INICIO)->first();

            $macro = App::make('SuperMacroService')->getMacroEspecializada($request, $sala);

            $sala->macro_id = $macro->id;
            $sala->save();

            $configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
            $configSeparadorEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_SEPARADOR_EMAIL)->first();
            if (config('app.debug')) {
                return view('email.email', ['sala' => $sala, 'email' => ($configEmail == NULL ? "" : $configEmail->valor )]);
            }
            else {
                Mail::to(array_map('trim', explode($configSeparadorEmail, $sala->email)))
                    ->cc($configEmail != null ? array_map('trim', explode($configSeparadorEmail, $configEmail->valor)) : "")
                    ->send(new SendMailUser($sala));

                return Redirect::action('SalaController@success');
            }
            
            //return compact($a);
        }
    }

    public function success() {
        //$sala = Sala::find($salaId);
        return view('salas.sucesso');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sala = SalaOld::findOrFail($id);
        return view("salas.visualiza-sala", ['sala' => $sala]);
        //return $sala;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  SalaOld  $sala
     * @return \Illuminate\Http\Response
     */
    public function edit(SalaOld $sala)
    {
        return view("salas.altera-sala",['sala' => $sala]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  SalaOld  $sala
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SalaOld $sala)
    {
        if ($sala->status->chave != Status::STATUS_ANALISE || $sala->status->chave != Status::STATUS_PROCESSO) {
            abort(403, 'Não pode alterar uma sala que já está finalizada!');
            return;
        }

        // validate
        $validator = Validator::make($request->all(), $this->getValidationRules(true));

        // process the login
        if ($validator->fails()) {
            abort(403, 'Erro de Validação');
        } else {
            $sala->nome_professor = $request->input('nome_professor');
            $sala->email = $request->input('email');
            $sala->faculdade = $request->input('faculdade');
            $sala->curso = $request->input('curso');
            $sala->nome_sala = $request->input('nome_sala');
            $sala->modalidade = $request->input('modalidade');
            $sala->objetivo_sala = $request->input('objetivo_sala');
            $sala->senha_aluno = $request->input('senha_aluno');
            $sala->senha_professor = $request->input('senha_professor');
            $sala->observacao = $request->input('observacao');
            $sala->save();

            /*$configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
            Mail::to($sala->email)
                ->cc($configEmail != null ? $configEmail->valor : "")
                ->send(new SendMailUser($sala));*/

            return redirect()->action('SalaController@index');
            //return compact($a);
        }
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
