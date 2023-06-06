<?php

namespace App\Http\Controllers;

use App\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Adldap\Laravel\Facades\Adldap;
use Illuminate\Support\Facades\Validator;
use Session;
use App\User;
use Exception;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR)->except(['usuarioLogado', 'list']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$u = Usuario::with(['setor:id,sigla,campus_id'])->get(['id','login','nome','email','permissao','setor_id']);
        //$u = Usuario::all();
        //return $u;
        //return view('config.usuarios',['usuarios' => $this->all()]);
        return view("layouts.app-angular");
    }
    public function all()
    {
        return User::all();
    }
    public function list()
    {
        $logado = $this->usuarioLogado();
        if ($logado->isAdmin())
            return User::all();
        else
            return [];
    }

    public function usuarioLogado() {
        return Auth::user();
    }

    public function usuarioEmail($uid) {
        $usuario = User::find($uid);
        $email = "";
        if($usuario) {
            try {
                $usuarioLdap = Adldap::search()->users()->find($usuario->email);
                if ($usuarioLdap != NULL && isset ($usuarioLdap->getAttributes()["mail"]))
                    $email = strtolower ($usuarioLdap->getAttributes()["mail"][0]);
            } catch (Exception $e) {

            }
        }

        return $email;
    }

    public function getInfosLdapServidorByDescription($cpf, $createUser = false) {
        $STRING_BAN = "Aluno";
        if (strlen($cpf) != 11 && strlen($cpf) != 14)
            return null;
        if (strlen($cpf) == 11)
            $cpf = substr($cpf,0,3).".".substr($cpf,3,3).".".substr($cpf,6,3)."-".substr($cpf,9,2);
        $us = Adldap::search()->where('description', '=', $cpf)->get();
        $u = null;
        if (count($us)) {
            if (count($us) > 1) {
                for ($i = 0; $i < count($us); $i++) {
                    $brk = true;
                    $listas = [];
                    if (!isset($us[$i]->getAttributes()["memberof"]))
                        $brk = false;
                    else
                        $listas = $us[$i]->getAttributes()["memberof"];
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
                return null;
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
            return $usuarioLDAP;
        }
        return null;
    }

    private function getValidationRules() {
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'name'        => 'required',
            'email'       => 'required',
            //'email'       => 'required|email',
            //'permissao'   => 'required|permissao',
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
        $validator = Validator::make($request->all(), $this->getValidationRules(true));

        // process the login
        if ($validator->fails()) {
            abort(403, 'Erro de Validação');
        }

        $usuario = new User();
        $usuario->name = $request->input('name');
        $usuario->email = $request->input('email');
        $usuario->password = 'not set';
        $usuario->permissao = User::PERMISSAO_USUARIO;
        $usuario->save();

        return User::all();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $usuario
     * @return \Illuminate\Http\Response
     */
    public function show(User $usuario)
    {

        $us = User::findOrFail($usuario);

        return $us;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $usuario
     * @return \Illuminate\Http\Response
     */
    public function edit(User $usuario)
    {
        //$us = Usuario::findOrFail($usuario);
        //return $usuario->with(['setor:id,sigla,campus_id'])->get(['id','login','nome','email','permissao','setor_id']);
        return $usuario;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $usuario)
    {
        //$usuario->nome = $request->input('nome');
        //$usuario->email = $request->input('email');
        $usuario->permissao = $request->input('permissao');
        $usuario->save();

        return $usuario;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function destroy(Usuario $usuario)
    {
        if ($usuario->delete()) {
            return new Usuario();
        }
        else {
            abort(404, 'Usuário não encontrado');
        }
    }

    public function getFirstLastNameUser() {
        $user = Auth::user();
        $name = explode(" ", $user->name);
        $firstName = $name[0];
        $lastname = $name[count($name)-1];

        return $firstName. ' ' . $lastname;

    }
}
