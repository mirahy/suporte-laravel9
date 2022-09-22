<?php

namespace App\Http\Middleware;

use Closure;
use \App\User;
use Illuminate\Support\Facades\Auth;

class Permissao
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$permissao)
    {
        $permitido = false;
        $user = Auth::user();
        if ($user != NULL) {
            if (is_array($permissao)) {
                foreach ($permissao as $p) {
                    if ($p == $user->permissao) {
                        $permitido = true;
                        break;
                    }
                }
            }
            elseif (is_string($permissao)) {
                $permitido = $permissao == $user->permissao;
            }
        }

        if ($permitido)
            return $next($request);
        abort(401, "O usuário não possui permissão para acessar esta página!");
    }
}
