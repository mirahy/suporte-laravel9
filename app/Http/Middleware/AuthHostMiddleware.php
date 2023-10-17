<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\ResourcesService;

class AuthHostMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, )
    {
        
        $ip = $request->ip();
        if($ip === "::1")
            $ip = "127.0.0.1";

        foreach(ResourcesService::IPS_VALIDOS as $ipValido) { 
             if(ResourcesService::ip_in_range($ip, $ipValido))
                return $next($request);
        }
        
        abort(401, "Este host não está habilitado para acessar este recurso!");
    }
}
