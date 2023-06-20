<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR);
    }

    public function conexao() {
        return "OK";
    }


    public function show() {
        return view('config.server');
    }
}
