<?php

namespace App\Http\Middleware;

use App\Exceptions\ExceptionSystem;
use App\Models\Usuario;
use Carbon\Carbon;
use Closure;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     * @throws ExceptionSystem
     */
    public function handle(Request $request, Closure $next)
    {
        $authorization = $request->header('Authorization', '');
        if ( preg_match('/^Bearer (\S+)$/', $authorization, $salida)) {
            $jwt = $salida[1];
            try{
                $request->merge(['userTokenBearer' => $jwt]);
                JWT::$leeway += 60;
                $token = JWT::decode($jwt, new Key(config('app.login_api_key'), 'HS512'));
                $now = new Carbon();
                $serverName = config('app.name');
                if ($token->iss !== $serverName ||
                    $token->nbf > $now->getTimestamp() ||
                    $token->exp < $now->getTimestamp())
                {
                    throw ExceptionSystem::createException( 'No autorizado(TOKEN VENCIDO)','tkn_vnc', "No autorizado",Response::HTTP_FORBIDDEN);
                }
                $usuario = Usuario::find($token->data->userId);
                if (!$usuario) {
                    throw ExceptionSystem::createException( 'El usuario ya no existe','tkn_inv', "No autorizado",Response::HTTP_FORBIDDEN);
                }
            }catch (ExpiredException | SignatureInvalidException | UnexpectedValueException $e){
                throw ExceptionSystem::createException( 'No autorizado(TOKEN Invalido)','tkn_inv', "No autorizado",Response::HTTP_FORBIDDEN);
            }
            $request->setUserResolver(function () use ($usuario) {
                return $usuario;
            });
            return $next($request);
        } else {
            throw ExceptionSystem::createException( 'No autorizado(TOKEN no encontrado en header)','tkn_inv', "No autorizado",Response::HTTP_FORBIDDEN);
        }
    }
}
