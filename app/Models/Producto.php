<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string url
 * @see Producto::getUrlAttribute()
 * @see Producto::setUrlAttribute()
 *
 * @property mixed $codigo
 * @property int costo
 * @property mixed $descripcion
 * @property mixed $nombre
 * @property mixed $precio
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
    const COLUMNA_ARCHIVO_ID = 'archivo_id';

    const COLUMNA_VIRTUAL_URL = 'url';

    const RELACION_IMAGEN = 'imagen';

    protected $appends = [
        self::COLUMNA_VIRTUAL_URL
    ];

    protected $guarded = [];

    public function imagen(): BelongsTo
    {
        return $this->belongsTo(Archivo::class, self::COLUMNA_ARCHIVO_ID);
    }

    public function getUrlAttribute(): string
    {
        return "https://joeschmoe.io/api/v1/random";
    }

    public function setUrlAttribute($url)
    {

    }

    public function asociarImagen64($url)
    {
        $archivoNuevo = Archivo::nuevoArchivo(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$url)), ($this->nombre?:'sinNombre') . '.jpg');
        $this->imagen()->associate($archivoNuevo);
    }

}
