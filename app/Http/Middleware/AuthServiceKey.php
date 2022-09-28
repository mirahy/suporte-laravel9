<?php

namespace App\Http\Middleware;

use Closure;

class AuthServiceKey
{

    private function validarChaveWebService($chave) {
        $chaveProcess = base64_encode(hash_hmac('sha256',base64_decode($chave),true));
        $contrachave =  env('CONTRACHAVE_WEBSERVICE_SUPORTE', '');
        if ($contrachave == '' || $contrachave == "*")
            return true;
        return $chaveProcess == $contrachave;
    }
     /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $chave = $request->has('chaveWebServiceSuporte') ? $request->input('chaveWebServiceSuporte') : null;
        if ($chave && $this->validarChaveWebService($chave))
            return $next($request);
        
        abort(401, "Você não está habilitado para acessar este recurso!");
    }
}
