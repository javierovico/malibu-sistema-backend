<?php

namespace App\Models;


use App\Exceptions\ExceptionSystem;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property mixed $password
 * @property mixed $id
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

    protected $hidden = [
        self::COLUMNA_PASSWORD
    ];

    public static function nuevoUsuario($usuario, $password): self
    {
        $nuevo = new self();
        $nuevo->user = $usuario;
        $nuevo->password = $password;
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

    /**
     * @throws ExceptionSystem
     */
    public function asignarRol($rol, $guardar = true)
    {
        if (is_string($rol)) {  //se trata del code
            $rol = Rol::getByCode($rol);
        } else if (is_int($rol)) {
            $rol = Rol::getById($rol);
        } else if (! $rol instanceof Rol) {
            $rol = null;
        }
        if (!$rol) {
            throw ExceptionSystem::createException('Especificacion de rol no encontrada', 'rolNotWork', 'Rol No compatible');
        }
        $this->roles()->attach($rol);
        if ($guardar) {
            $this->save();
        }
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, RolUsuario::tableName, RolUsuario::COLUMNA_USUARIO_ID, RolUsuario::COLUMNA_ROL_ID)
            ->withTimestamps()
        ;
    }

}
