<?php

namespace App\Http\Controllers;

use App\Exceptions\ExceptionSystem;
use App\Models\Usuario;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @throws ExceptionSystem
     */
    public function login(Request $request)
    {
        $request->validate([
            'user' => 'required',
            'password' => 'required',
            'dias' => ''
        ]);
        $dias = $request->get('dias', 1);
        $password = $request->get('password');
        $usuario = Usuario::getByUsername($request->user);
        if (!$usuario || !$usuario->comparePassword($password)) {
            throw ExceptionSystem::createException("Usuario o password incorrecto",'UserNotFound','Eror En Usuario', Response::HTTP_UNAUTHORIZED);
        } else {
            $token = $usuario->generateToken($dias);
            return self::respuestaDTOSimple('login',"Usuario logueado","login",[
                'type' => 'Bearer',
                'token' => $token,
                'expires' => CarbonImmutable::make('now')->timezone('UTC')->addDays($dias)
            ]);
        }
    }

    public function getUser(Request $request)
    {
        /** @var Usuario $user */
        $user = $request->user();
        $user->load('roles');
        return self::respuestaDTOSimple('getuser', 'Usuario obtenido desde token', 'getUser',
            ['usuario' => $user]);
    }
}
