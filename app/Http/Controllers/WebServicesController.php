<?php

namespace App\Http\Controllers;

use App\GrupoLotesSimplificado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

class WebServicesController extends Controller
{
    public function __construct()
    {
        $this->middleware('authhost');
    }

    public function consultaDadosCartao (Request $request, $documento) {
        return App::make('SigecadService')->consultaDadosCartao($documento);
    }

    public function exportaEstudantesGrupoLotes (Request $request) {
        $respostas = "";
        foreach (GrupoLotesSimplificado::all() as $grupo)
            if ($grupo->auto_export_estudantes)
                $respostas .= App::make('GrupoLotesSimplificadoService')->insereEstudantes($request, $grupo) . "<br>";
        return $respostas;
    }
}
