<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'user' => 'required',
            'password' => 'required',
        ]);
        $usuario = Usuario::getFromUserAndPassword($request->user, $request->password);
        return $usuario;
    }
}
