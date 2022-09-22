<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Agenda;
use App\User;

class AgendaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('authdev:RubensMarcon');
        $this->middleware('permissao:'.User::PERMISSAO_ADMINISTRADOR);
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

    public function listar() {
        return Agenda::all();
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

        // process the login
        if ($validator->fails()) {
            abort(403, 'Erro de Validação');
        } else {
            //$a = Usuario::create($request->all());
            $agenda = new Agenda();
            $agenda->title = $request->input('title');
            $agenda->start = $request->input('start');
            $agenda->end = $request->input('end');
            $agenda->allDay = $request->input('allDay');
            $agenda->maisDay = $request->input('maisDay');
            $agenda->backgroundColor = $request->input('backgroundColor');
            $agenda->save();
  
            return $agenda;
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
     * @param  Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Agenda $agenda)
    {
        // validate
        $validator = Validator::make($request->all(), $this->getValidationRules(true));

        // process the login
        if ($validator->fails()) {
            abort(403, 'Erro de Validação');
        } else {
            $agenda->title = $request->input('title');
            $agenda->start = $request->input('start');
            $agenda->end = $request->input('end');
            $agenda->allDay = $request->input('allDay');
            $agenda->maisDay = $request->input('maisDay');
            $agenda->backgroundColor = $request->input('backgroundColor');
            $agenda->save();
  
            return $agenda;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Agenda $agenda)
    {
        if ($agenda->delete()) {
            return new Agenda();
        }
        else {
            abort(404, 'Evento não encontrado');
        }
    }
}
