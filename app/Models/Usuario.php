<?php

namespace App\Models;


use App\Exceptions\ExceptionSystem;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property mixed $password
 * @property mixed $id
 * @property mixed $nombre
 * @property mixed $user
 */
class Usuario extends ModelRoot
{
    const tableName = 'usuarios';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_USER = 'user';
    const COLUMNA_PASSWORD = 'password';
    const COLUMNA_NOMBRE = 'nombre';

    protected $hidden = [
        self::COLUMNA_PASSWORD
    ];

    public static function nuevoUsuario($usuario, $nombre, $password): self
    {
        $nuevo = new self();
        $nuevo->user = $usuario;
        $nuevo->password = $password;
        $nuevo->nombre = $nombre;
        $nuevo->save();
        return $nuevo;
    }

    public static function getByUsername($user): ?self
    {
        return self::where(self::COLUMNA_USER, $user)->first();
    }

    public function setPasswordAttribute($att)
    {
        $this->attributes[self::COLUMNA_PASSWORD] = Hash::make($att);
    }

    public function comparePassword($pass): bool
    {
        return Hash::check($pass, $this->password);
    }

    public function generateToken($dias): string
    {
        $userId =  $this->id;
        $secretKey  = config('app.login_api_key');
        $tokenId    = base64_encode(random_bytes(16));
        $issuedAt   = new Carbon();
        $expire     = (new Carbon($issuedAt))->addDays($dias)->getTimestamp();
        $serverName = config('app.name');
        return JWT::encode(
            [
                'iat'  => $issuedAt->getTimestamp(),    // Issued at: time when the token was generated
                'jti'  => $tokenId,                     // Json Token Id: an unique identifier for the token
                'iss'  => $serverName,                  // Issuer
                'nbf'  => $issuedAt->getTimestamp(),    // Not before
                'exp'  => $expire,                      // Expire
                'data' => [                             // Data related to the signer user
                    'userId' => $userId,
                ]
            ],      //Data to be encoded in the JWT
            $secretKey, // The signing key
            'HS512'     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );
    }

}
