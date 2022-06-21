<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $codigo
 * @property mixed $descripcion
 */
class Rol extends ModelRoot
{
    const tableName = 'roles';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_CODIGO = 'codigo';
    const COLUMNA_DESCRIPCION = 'descripcion';

    protected $guarded = [];

    const ROL_ADMIN_PRODUCTOS = 'admin_productos';
    const ROL_VISOR_INGRESOS = 'visor_ingresos';

    const ROLES_INICIALIZADOS = [
        self::ROL_ADMIN_PRODUCTOS => [
            self::COLUMNA_DESCRIPCION => 'Administra Los productos disponibles',
        ],
        self::ROL_VISOR_INGRESOS => [
            self::COLUMNA_DESCRIPCION => 'Visualiza los ingresos',
        ],
    ];

    public static function inicializar()
    {
        collect(self::ROLES_INICIALIZADOS)->each(function ($item, $codigo) {
            $rol = new self($item);
            $rol->codigo = $codigo;
            $rol->save();
        });
    }

    public static function getByCode(string $rol): ?self
    {
        return self::where(self::COLUMNA_CODIGO, $rol)->first();
    }

    public static function getById(int $rol): ?self
    {
        return self::find($rol);
    }
}
