<?php 

namespace App\Services;
 
use App\User;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResourcesService
{
    public $permissao;
    public $keepUser = false;
    
    public function __construct(Request $request)
    {
        $user = Auth::user();
        $perm = User::PERMISSAO_INATIVO;
        $ip = $request->ip();
        if($ip === "::1")
            $ip = "127.0.0.1";

        if ($user != null) {
            $perm = $user != null ? $user->permissao : User::PERMISSAO_INATIVO;
        }

        foreach (self::IPS_VALIDOS as $ipValido) { 
            if ($this->ip_in_range($ip, $ipValido))
                $this->keepUser = true;
        }
        $this->permissao = $perm;
    }

    const IPS_VALIDOS = [
        "127.0.0.1/32",
        "200.129.209.0/24",
        "200.129.215.0/24",
        "172.22.0.0/24"
    ];


    public static function ip_in_range( $ip, $range ) {

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
 
}