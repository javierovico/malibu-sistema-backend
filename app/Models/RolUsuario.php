<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolUsuario extends ModelRoot
{
    const tableName = 'rol_usuario';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_ROL_ID = 'rol_id';
    const COLUMNA_USUARIO_ID = 'usuario_id';
}
