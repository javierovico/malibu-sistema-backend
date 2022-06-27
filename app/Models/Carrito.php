<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrito extends ModelRoot
{
    const tableName = 'carrito';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_CLIENTE_ID = 'cliente_id';
    const COLUMNA_FECHA_CREACION = 'fecha_creacion';
    const COLUMNA_PAGADO = 'pagado';
    const COLUMNA_MESA_ID = 'mesa_id';
    const COLUMNA_IS_DELIVERY = 'is_delivery';

}
