<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Crypt;
use Closure;
use Illuminate\Http\Request;

class PassMoodle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, )
    {
        // guardar na sessão a senha criptografada para login no moodle.
        $crypt = new Crypt;
        $pass = '';
        if($request->has('password'))
            $pass = $crypt->encrypt($request->get('password'));
            $request->session()->put('pass', $pass);
        return $next($request);
    }
}
