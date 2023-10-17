<?php

namespace App\Http\Controllers;

use App\Mail\SendMailReserva;
use App\Recurso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Reserva;
use App\Status;
use App\User;
use App\Configuracoes;
use Illuminate\Support\Facades\Auth;
use Adldap\Laravel\Facades\Adldap;
use Illuminate\Support\Facades\Mail;

class ReservasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('authdev:RubensMarcon');
        //$this->middleware('authdev:JoaoNormando');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR.','.User::PERMISSAO_SERVIDOR);
        $this->middleware('gestor')->except(['index', 'listar', 'usuarioLogado','store','cancelar','recurso']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        self::setSession($request);
        return view("layouts.app-angular");
    }

    public static function setSession(Request $request) {
        $recurso = null;
        if (!$request->session()->has('recurso')) {
            $recurso = Recurso::find(1);
            if ($recurso){
                $request->session()->put('recurso', $recurso);
                $isGestor = false;
                $user = Auth::user();
                if (!$request->session()->has('isGestor')) {
                    foreach ($recurso->gestoresRecursos as $gestor) {
                        if ($gestor->id == $user->id) {
                            $isGestor = true;
                            break;
                        }
                    }
                    $request->session()->put('isGestor', $isGestor);
                } 
            }
        }
        
    }

    public function recurso(Request $request) {
        return $request->session()->get('recurso');
    }

    public function listar(Request $request) {
        $isGestor = $request->session()->get('isGestor');
        $user = Auth::user();
        if ($isGestor) {
            return Reserva::all();
        }
        else {
            $reservas = Reserva::all();
            $reservasFiltradas = [];
            $stDeferido = Status::where('chave', Status::STATUS_DEFERIDO)->first();
            $stAnalise = Status::where('chave', Status::STATUS_ANALISE)->first();
            foreach ($reservas as $r) {
                if($r->status->id == $stDeferido->id || $r->status->id == $stAnalise->id || $r->usuario_id == $user->id)
                    $reservasFiltradas[] = $r;
            }
            return $reservasFiltradas;
        }
    }

    public function usuarioLogado(Request $request) {
        $user = Auth::user();
        $isGestor = $request->session()->get('isGestor');
        $user['gestor'] = $isGestor;
        return $user;
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

    private function getValidationRules($full = false) {
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'title'             => 'required',
            'start'             => 'required'
        );
        return $rules;
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

        $user = Auth::user();

        $recurso = $request->session()->get('recurso');

        if (!$recurso) {
            abort(400, "Nenhum Recurso Selecionado");
        }

        // process the login
        if ($validator->fails()) {
            abort(403, 'Erro de Validação');
        } else {
            //$a = Usuario::create($request->all());
            $reserva = new Reserva();
            $reserva->title = $request->input('title');
            $reserva->start = $request->input('start');
            $reserva->end = $request->input('end');
            $reserva->allDay = $request->input('allDay');
            $reserva->maisDay = $request->input('maisDay');
            $reserva->backgroundColor = $request->input('backgroundColor');
            $reserva->recurso_id =  $recurso->id;
            $reserva->observacao = $request->input('observacao');
            $reserva->justificativa = $request->input('justificativa');
            $reserva->status = Status::where('chave', Status::STATUS_ANALISE)->first();
            $reserva->usuario_id = $user->id;
            $reserva->save();
  
            return $this->enviaEmail($reserva);

            //return $reserva;
        }
    }

    private function enviaEmail(Reserva $reserva, $update = FALSE) {
        $user = Auth::user();
        $email = null;
        $usuarioLdap = Adldap::search()->users()->find($user->email);
        if ($usuarioLdap != NULL && isset ($usuarioLdap->getAttributes()["mail"]))
            $email = strtolower ($usuarioLdap->getAttributes()["mail"][0]);

        $configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
        $configSeparadorEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_SEPARADOR_EMAIL)->first();

        $emailsGestores = [];
        foreach ($reserva->recurso->gestoresRecursos as $gestor) {
            $usuarioLdap = Adldap::search()->users()->find($gestor->email);
            $emailTemp = null;
            if ($usuarioLdap != NULL && isset ($usuarioLdap->getAttributes()["mail"]))
                $emailTemp = strtolower ($usuarioLdap->getAttributes()["mail"][0]);
            if ($emailTemp)
                $emailsGestores[] = $emailTemp;
        }

        if (config('app.debug')) {
            $timezone = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_TIMEZONE)->first();
            return $reserva;
            return view('email.email-reserva', ['reserva' => $reserva, 'email' => ($configEmail == NULL ? "" : $configEmail->valor ), 'gestores' => $emailsGestores, 'timezone' => $timezone->valor]);
        }
        else {
            Mail::to($email == null ? ($configEmail != null ? array_map('trim', explode($configSeparadorEmail, $configEmail->valor)) : "") : $email)
                ->cc($emailsGestores)
                ->send(new SendMailReserva ($reserva, $update));

            return $reserva;
        }
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
     * @param  Reserva  $reserva
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reserva $reserva)
    {
        // validate
        $validator = Validator::make($request->all(), $this->getValidationRules(true));

        // process the login
        if ($validator->fails()) {
            abort(403, 'Erro de Validação');
        } else {
            $reserva->title = $request->input('title');
            $reserva->start = $request->input('start');
            $reserva->end = $request->input('end');
            $reserva->allDay = $request->input('allDay');
            $reserva->maisDay = $request->input('maisDay');
            $reserva->backgroundColor = $request->input('backgroundColor');
            $reserva->save();
  
            return $this->enviaEmail($reserva, true);

            //return $reserva;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reserva $reserva)
    {
        if ($reserva->delete()) {
            return new Reserva();
        }
        else {
            abort(404, 'Evento não encontrado');
        }
    }

    public function cancelar(Request $request, $reservaId = NULL, $justificativa = NULL) {
        if ($reservaId == null)
            $reservaId = $request->input('reservaId');
        $reserva = NULL;
        if (!$reservaId || !$reserva = Reserva::find($reservaId)) {
            abort(400, "Nenhuma reserva selecionada!");
            return;
        }
        $usuarioLogado = $this->usuarioLogado($request);
        if (!$usuarioLogado['gestor'] && $reserva->usuario_id != $usuarioLogado->id)
            abort(401, "Não autorizado a realizar este cancelamento!");
        if ($justificativa == NULL)
            $justificativa = $request->input('justificativa');
        $reserva->status = Status::where('chave', Status::STATUS_CANCELADO)->first();
        $reserva->justificativa = $justificativa;
        $reserva->save();
        return $this->enviaEmail($reserva, false);
        //return $reserva;
    }
    
    public function analiseGestor(Request $request, $reservaId = NULL, $status = NULL, $justificativa = NULL)
    {
        if ($reservaId == null)
            $reservaId = $request->input('reservaId');
        $reserva = NULL;
        if (!$reservaId || !$reserva = Reserva::find($reservaId)) {
            abort(400, "Nenhuma reserva selecionada!");
            return;
        }
        if ($status == NULL) 
            $status = $request->input('status');
        if ($justificativa == NULL)
            $justificativa = $request->input('justificativa');
        switch ($status) {
            case Status::STATUS_ANALISE:
                $reserva->status = Status::where('chave', Status::STATUS_ANALISE)->first();
                break;
            case Status::STATUS_DEFERIDO:
                $reserva->status = Status::where('chave', Status::STATUS_DEFERIDO)->first();
                break;
            case Status::STATUS_INDEFERIDO:
            $reserva->status = Status::where('chave', Status::STATUS_INDEFERIDO)->first();
                break;
            default: 
                abort(404, "Status Inválido!");
                return;
        }
        $reserva->justificativa = $justificativa;
        $reserva->save();
        return $this->enviaEmail($reserva, false);
        //return $reserva;
    }
}
