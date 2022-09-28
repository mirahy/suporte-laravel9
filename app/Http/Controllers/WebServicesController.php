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
        $this->middleware('authservicekey')->only(['consultaDadosCartao']);
        $this->middleware('authhost')->only(['exportaEstudantesGrupoLotes']);
    }

    public function consultaDadosCartao (Request $request) {
        $documento = $request->has('documento') ? $request->input('documento') : null;
        if (!$documento)
            abort(400, "Parâmetros inválidos");
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
