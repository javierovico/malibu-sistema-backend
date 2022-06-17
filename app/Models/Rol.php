<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends ModelRoot
{
    const tableName = 'roles';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_CODIGO = 'codigo';
    const COLUMNA_DESCRIPCION = 'descripcion';

    const ROL_ADMIN_PRODUCTOS = 'admin_productos';

    const ROLES_INICIALIZADOS = [
        self::ROL_ADMIN_PRODUCTOS => [
            self::COLUMNA_CODIGO => self::ROL_ADMIN_PRODUCTOS,
            self::COLUMNA_DESCRIPCION => 'Administra Los productos disponibles',
        ],
    ];

    public static function inicializar()
    {

    }
}
