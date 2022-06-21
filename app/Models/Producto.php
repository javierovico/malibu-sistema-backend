<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string url
 * @property mixed $codigo
 * @property int costo
 * @see Producto::getUrlAttribute()
 */
class Producto extends ModelRoot
{
    const tableName = 'productos';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_CODIGO = 'codigo';
    const COLUMNA_NOMBRE = 'nombre';
    const COLUMNA_DESCRIPCION = 'descripcion';
    const COLUMNA_PRECIO = 'precio';
    const COLUMNA_COSTO = 'costo';
    const COLUMNA_S3_KEY = 's3_key';

    const COLUMNA_VIRTUAL_URL = 'url';

    protected $appends = [
        self::COLUMNA_VIRTUAL_URL
    ];

    protected $guarded = [];

    public function getUrlAttribute(): string
    {
        return "https://joeschmoe.io/api/v1/random";
    }

}
