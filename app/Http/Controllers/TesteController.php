<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Adldap\Laravel\Facades\Adldap;
use Illuminate\Support\Facades\Mail;
use \App\Usuario;
use \App\User;
use \App\Patrimonio;
use \App\Mail\SendMailUser;
use App\Sala;
use App\Configuracoes;
use Illuminate\Support\Facades\App;

class TesteController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR);
    }

    public function conexao() {
        return "OK";
    }


    public function show($id) {
        return view('teste', ['id' => $id]);
    }

    public function find ($id) {
        //Mail::to("rubensantoniomarcon@gmail.com")->send(new SendMailUser());
        return view('teste', [
            'user' => Adldap::search()->users()->find($id), 
            'outro' => Auth::user(),
            'u1' => User::where('email', $id)->get()->first(),
            'u2' => Auth::once(['email'=>'rubensmarcon', 'password' => '123'])
        ] );
        //$user = Adldap::search()->users()->find('john doe');
    }

    public function buscaUser ($id, $createUser = true) {
        if (strlen($id) == 11)
            $id = substr($id,0,3).".".substr($id,3,3).".".substr($id,6,3)."-".substr($id,9,2);
        $STRING_BAN = "Aluno";
        $us = Adldap::search()->where('description', '=', $id)->get();
        $u = null;
        if (count($us)) {
            if (count($us) > 1) {
                for ($i = 0; $i < count($us); $i++) {
                    $listas = $us[$i]->getAttributes()["memberof"];
                    $brk = true;
                    foreach ($listas as $l) {
                        if (is_int(strpos ($l, $STRING_BAN))){
                            $brk = false;
                            break;
                        }
                    }
                    if ($brk) {
                        $u = $us[$i];
                        break;
                    }
                }
            }
            else 
                $u = $us[0];
            
            if (!$u)
                return "Usuário acadêmico";   
            $usuarioLDAP = [
                'userId' => 0, 
                'email' => $u->getAttributes()["mail"][0], 
                'username' => $u->getAttributes()["samaccountname"][0],
                'nome' => $u->getAttributes()["displayname"][0],
            ]; 
            if ($createUser) {
                $usuario = User::where('email', $usuarioLDAP['username'])->first();
                if ($usuario)
                    $usuarioLDAP['userId'] = $usuario->id;
                else {
                    $usuario = new User();
                    $usuario->name = $usuarioLDAP['nome'];
                    $usuario->email = $usuarioLDAP['username'];
                    $usuario->password = 'not set';
                    $usuario->permissao = User::PERMISSAO_USUARIO;
                    $usuario->save();
                    $usuarioLDAP['userId'] = $usuario->id;
                }
            }
            return view('teste', [
                'user' => $u, 
                'outro' => $usuarioLDAP["email"], //$u[1]->getAttributes()["description"][0],
                'u1' => $usuarioLDAP["username"],
                'u2' => $usuarioLDAP["nome"],
                'u3' => $usuarioLDAP["userId"],
            ] );




        }
        return "Não encontrado";
    }

    public function est($salaId) {
        $sala = Sala::find($salaId);
        if ($sala == NULL)
            abort(404, "Sala não Encontrada");
        

        echo "<pre>";
        
        var_dump ($sala->getCategoriaId());
        echo "</pre>";
    }

    public function erro($erro) {
        abort($erro, "Alguma coisa errada");
    }

    public function us($siape){
        $a = Usuario::find(88);
        $a = Usuario::where('login', $siape)->get();
        $e = Patrimonio::all()[0];
        return view('us', ['usuario' =>  $a, 'estado' => $e]);
    }
    public function email($salaId, $status, $mensagem = NULL){
        $sala = Sala::find($salaId);
        $sala->mensagem = $mensagem;
        $configEmail = Configuracoes::where('nome', Configuracoes::CONFIGURACAO_EMAIL_SUPORTE)->first();
        return view('email.email', ['sala' => $sala, 'email' => ($configEmail == NULL ? "" : $configEmail->valor )]);
    }

    public function sigecad(Request $request, $codigoDisciplina,$periodoLetivoKey, $turmaKey){
        //return App::make('SigecadService')->executarDisciplinas([], [], "SELECT * FROM secretaria.disciplinas_para_moodle LIMIT 10");
        return App::make('SigecadService')->getAcademicosDisciplina($request, $codigoDisciplina, $periodoLetivoKey, $turmaKey);
        //return App::make('SigecadService')->getDisciplinasList($request,'FAIND',  $id_sigecad);
        return App::make('SigecadService')->consulta($request);
    }

    public function setUser($siape) {
        $user = User::where('email', $siape)->get()->first();
        $a = Auth::loginUsingId($user->id);
        Auth::login($a);
        return view('us', [
            'usuario' => Auth::user(),
            'u1' => Usuario::where('login', $siape)->get()->first(),
            'u2' => $a,
        ] );
    }
}
