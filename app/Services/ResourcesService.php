<?php 

namespace App\Services;
 
use App\User;
use Illuminate\Support\Facades\Auth;

class ResourcesService
{
    public $permissao;
     
    public function __construct()
    {
        $user = Auth::user();
        $perm = User::PERMISSAO_INATIVO;
        if ($user != null) {
            $perm = $user != null ? $user->permissao : User::PERMISSAO_INATIVO;
        }
        $this->permissao = $perm;
    }
 
}