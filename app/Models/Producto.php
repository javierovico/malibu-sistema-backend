<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property mixed $codigo
 * @property int costo
 * @property mixed $descripcion
 * @property mixed $nombre
 * @property mixed $precio
 * @property mixed $id
 * @property CarritoProducto $carritoProducto
 * @see Producto::getCarritoProductoAttribute()
 * @property Pivot $pivot
 * @see Producto::getUrlAttribute()
 * @method static self find(mixed $idAgrega)
 * @method static self findOrFail(mixed $idAgrega)
 */
class Producto extends ModelRoot
{
    use SoftDeletes;
    const tableName = 'productos';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_TIPO_PRODUCTO = 'tipo_producto_id';
    const COLUMNA_CODIGO = 'codigo';
    const COLUMNA_STOCK = 'stock';
    const COLUMNA_NOMBRE = 'nombre';
    const COLUMNA_DESCRIPCION = 'descripcion';
    const COLUMNA_PRECIO = 'precio';
    const COLUMNA_COSTO = 'costo';
    const COLUMNA_S3_KEY = 's3_key';
    const COLUMNA_ARCHIVO_ID = 'archivo_id';


    const RELACION_IMAGEN = 'imagen';
    const RELACION_TIPO_PRODUCTO = 'tipoProducto';
    const RELACION_PRODUCTO_COMBOS = 'productoCombos';

    protected $appends = [

    ];

    protected $attributes = [
        self::COLUMNA_DESCRIPCION => '',
        self::COLUMNA_STOCK => '1',
    ];

    protected $guarded = [];

    /**
     * Retorna una query que filtra por tipo de producto code
     * @param string $code
     * @param Builder|null $queryActual
     * @return Builder
     */
    public function getQueryByTipoProductocode(string $code, ?Builder $queryActual = null): Builder
    {
        if (!$queryActual) {
            $queryActual = Producto::query();
        }
        return $queryActual->whereHas(Producto::RELACION_TIPO_PRODUCTO,fn(Builder $b)=>$b->where(TipoProducto::COLUMNA_CODE,$code));
    }

    public static function getQueryProductoCombo(?Builder $queryActual = null): Builder
    {
        return self::getQueryByTipoProductocode(TipoProducto::TIPO_PRODUCTO_COMBO, $queryActual);
    }

    public static function getQueryProductoSimple(?Builder $queryActual = null): Builder
    {
        return self::getQueryByTipoProductocode(TipoProducto::TIPO_PRODUCTO_SIMPLE, $queryActual);
    }

    public static function getQueryProductoDelivery(?Builder $queryActual = null): Builder
    {
        return self::getQueryByTipoProductocode(TipoProducto::TIPO_PRODUCTO_DELIVERY, $queryActual);
    }

    public function getCarritoProductoAttribute(): ?CarritoProducto
    {
        $c = new CarritoProducto($this->pivot->attributes);
        $c->exists = true;
        return $c;
    }

    public function imagen(): BelongsTo
    {
        return $this->belongsTo(Archivo::class, self::COLUMNA_ARCHIVO_ID);
    }

    public function tipoProducto(): BelongsTo
    {
        return $this->belongsTo(TipoProducto::class, self::COLUMNA_TIPO_PRODUCTO);
    }

    public function productoCombos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, ComboProducto::tableName, ComboProducto::COLUMNA_PRODUCTO_ID, ComboProducto::COLUMNA_COMBO_ID);
    }

    public function asociarImagen64($url)
    {
        $archivoNuevo = Archivo::nuevoArchivo(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$url)), ($this->nombre?:'sinNombre') . '.jpg');
        $this->imagen()->associate($archivoNuevo);
    }

    public function borrarImagen()
    {
        $this->imagen()->dissociate();
    }

}
