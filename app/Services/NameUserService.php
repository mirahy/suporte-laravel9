<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Facades\Auth;

class NameUserService
{
    public $name;
    public $login;
    public $pass;

    public function __construct()
    {
        $user = Auth::user();
        // dd($user);
        if($user != null){
            $name = explode(" ", $user->name);
            $firstName = $name[0];
            $lastname = $name[count($name)-1];
            $this->name = $firstName. ' ' . $lastname;
            $this->login = $user->email;
            $this->pass = $user->password;
        }

    }

}
