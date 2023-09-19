<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CursosMoodleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function meusCursos()
    {
        return view("layouts.app-angular");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function todosCursos()
    {
        return view("layouts.app-angular");
    }
}
