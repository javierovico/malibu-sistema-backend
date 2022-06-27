<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComboProducto extends ModelRoot
{
    const tableName = 'combo_producto';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_COMBO_ID = 'combo_id';
    const COLUMNA_PRODUCTO_ID = 'producto_id';
}
