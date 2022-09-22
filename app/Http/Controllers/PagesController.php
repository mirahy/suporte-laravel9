<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\User;

class PagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('authdev:RubensMarcon');
    }
    public function index () {
        return "¯\_(ツ)_/¯";
    }
    private function getPermissao() {
        $user = Auth::user();
        $perm = User::PERMISSAO_INATIVO;
        if ($user != null) {
            $perm = $user != null ? $user->permissao : User::PERMISSAO_INATIVO;
        }
        return $perm;
    }
    public function home() {
        return view('home', ['permissao' => $this->getPermissao()]);
    }
    public function usuarios () {
        $perm = $this->getPermissao();
        if ($perm != User::PERMISSAO_ADMINISTRADOR)
            abort(401, "Não Autorizado!");
        return view('users.usuarios', ['permissao' => $perm]);
    }
    public function administracao() {
        $perm = $this->getPermissao();
        if ($perm != User::PERMISSAO_ADMINISTRADOR)
            abort(401, "Não Autorizado!");
        return view('inventario.administracao', ['permissao' => $perm]);
    }
    public function print () {
        $content = Input::get('content');
        if ($content == NULL)
            abort(400, "Conteúdo não informado");
        return view('print', ['content' => $content]);
    }
}
