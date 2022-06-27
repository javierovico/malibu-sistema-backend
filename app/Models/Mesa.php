<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesa extends ModelRoot
{
    const tableName = 'mesas';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_CODE = 'code';
    const COLUMNA_DISPONIBLE = 'disponible';
    const COLUMNA_DESCRIPCION = 'descripcion';
}
