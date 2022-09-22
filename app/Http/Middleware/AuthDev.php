<?php

namespace App\Http\Middleware;

use Closure;
use \App\User;
use Illuminate\Support\Facades\Auth;

class AuthDev
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $login)
    {
        $user = User::where('email', $login)->get()->first();
        if ($user!= null) {
            Auth::login(Auth::loginUsingId($user->id));
            if (Auth::user() != NULL)
                return $next($request);
        }
        abort(401, "Usuário '$login' não identificado!");
    }
}
