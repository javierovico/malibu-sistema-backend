<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends ModelRoot
{
    use SoftDeletes;
    const tableName = 'clientes';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_IMAGEN_ID = 'imagen_id';
    const COLUMNA_NOMBRE = 'nombre';
    const COLUMNA_TELEFONO = 'telefono';
    const COLUMNA_CIUDAD = 'ciudad';
    const COLUMNA_BARRIO = 'barrio';

}
