<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Facades\Auth;

class NameUserService
{
    public $name;

    public function __construct()
    {
        $user = Auth::user();
        if($user != null){
            $name = explode(" ", $user->name);
            $firstName = $name[0];
            $lastname = $name[count($name)-1];
            $this->name = $firstName. ' ' . $lastname;
        }

    }

}
