<?php

namespace App\Http\Controller;

use App\Models\User;

class UserController
{
    // Apenas para demonstração
    public function index()
    {
        $user = User::init();

        return $user->all();
    }
}
