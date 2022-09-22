<?php

namespace App\Http\Middleware;

use Closure;

class AuthHostMiddleware
{

    const IPS_VALIDOS = [
        "127.0.0.1/32",
        "200.129.209.0/24",
        "200.129.215.0/24",
        "172.22.0.0/24"
    ];


    private function ip_in_range( $ip, $range ) {

        if ( strpos( $range, '/' ) == false ) {
            $range .= '/32';
        }
        // $range is in IP/CIDR format eg 127.0.0.1/24
        list( $range, $netmask ) = explode( '/', $range, 2 );
        $range_decimal = ip2long( $range );
        $ip_decimal = ip2long( $ip );
        $wildcard_decimal = pow( 2, ( 32 - $netmask ) ) - 1;
        $netmask_decimal = ~ $wildcard_decimal;
        return ( ( $ip_decimal & $netmask_decimal ) == ( $range_decimal & $netmask_decimal ) );
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
        $ip = $request->ip();

        foreach (self::IPS_VALIDOS as $ipValido) {
            if ($this->ip_in_range($ip, $ipValido))
                return $next($request);
        }
        
        abort(401, "Este host não está habilitado para acessar este recurso!");
    }
}
