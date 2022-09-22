<?php

namespace App\Http\Middleware;

use App\Recurso;
use App\User;
use Illuminate\Support\Facades\Auth;
use Closure;

class VerificaGestorRecurso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $recurso = $request->session()->get('recurso');
        if ($recurso == null)
            abort(400, "Nenhum recurso Selecionado!");
        //$recurso = $recursoId ? Recurso::find($recursoId) : null;
        if ($request->session()->has('isGestor')) {
            if ($request->session()->get('isGestor'))
                return $next($request);
        }
        else {
            if ($user!= null && $recurso != null) {
                foreach ($recurso->gestoresRecursos as $gestor) {
                    if ($gestor->id == $user->id) {
                        return $next($request);
                    }
                }
            }
        }
        abort(401, "Usuário não Autorizado!");
    }
}
