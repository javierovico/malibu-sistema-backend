<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarritoProducto extends ModelRoot
{
    const tableName = 'carrito_producto';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_CARRITO_ID = 'carrito_id';
    const COLUMNA_PRODUCTO_ID = 'producto_id';
    const COLUMNA_ESTADO = 'estado';
    const COLUMNA_PRECIO = 'precio';
    const COLUMNA_COSTO = 'costo';
}
