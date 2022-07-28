<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @property mixed $fecha_creacion
 * @see Carrito::setFechaCreacionAttribute()
 * @see Carrito::getFechaCreacionAttribute()
 * @property mixed $status
 * @property Mesa $mesa
 * @property mixed $isActivo
 * @see Carrito::getIsActivoAttribute()
 * @property mixed $is_delivery
 * @property boolean $pagado
 * @property Collection $productos
 * @property mixed $id
 */
class Carrito extends ModelRoot
{
    const tableName = 'carrito';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_CLIENTE_ID = 'cliente_id';
    const COLUMNA_MOZO_ID = 'mozo_id';
    const COLUMNA_PRODUCTO_DELIVERY_ID = 'producto_delivery_id';
    const COLUMNA_FECHA_CREACION = 'fecha_creacion';
    const COLUMNA_PAGADO = 'pagado';
    const COLUMNA_MESA_ID = 'mesa_id';
    const COLUMNA_IS_DELIVERY = 'is_delivery';
    const COLUMNA_STATUS = 'status';

    protected $attributes = [
        self::COLUMNA_PAGADO => '0',
        self::COLUMNA_IS_DELIVERY => '0',
        self::COLUMNA_STATUS => self::ESTADO_CREADO,
    ];

    protected $casts = [
        self::COLUMNA_PAGADO => 'boolean',
        self::COLUMNA_IS_DELIVERY => 'boolean'
    ];

    const ESTADO_CREADO = 'creado';
    const ESTADO_MODIFICADO = 'modificado';
    const ESTADO_PAGADO = 'pagado';
    const ESTADO_FINALIZADO = 'finalizado';

    const ESTADOS_ACTIVOS = [
        self::ESTADO_CREADO,
        self::ESTADO_MODIFICADO,
        self::ESTADO_PAGADO,
    ];

    const RELACION_MESA = 'mesa';
    const RELACION_CLIENTE = 'cliente';
    const RELACION_MOZO = 'mozo';
    const RELACION_PRODUCTOS = 'productos';
    const RELACION_DELIVERY = 'delivery';

    /**
     * Crea sin guardar en la base de datos
     * @param Usuario $mozo
     * @param Mesa|null $mesa
     * @param Cliente|null $cliente
     * @return static
     */
    public static function nuevoCarrito(Usuario $mozo, ?Mesa $mesa = null, ?Cliente $cliente = null): self
    {
        $carrito = new Carrito();
        $carrito->fecha_creacion = CarbonImmutable::now();
        $carrito->mozo()->associate($mozo);
        if ($mesa) {
            $carrito->mesa()->associate($mesa);
        }
        if ($cliente) {
            $carrito->cliente()->associate($cliente);
        }
        return $carrito;
    }

    public function productos(): BelongsToMany
    {
        return $this
            ->belongsToMany(Producto::class,CarritoProducto::tableName, CarritoProducto::COLUMNA_CARRITO_ID, CarritoProducto::COLUMNA_PRODUCTO_ID)
            ->withPivot([
                CarritoProducto::COLUMNA_ID,
                CarritoProducto::COLUMNA_ESTADO,
                CarritoProducto::COLUMNA_CANTIDAD,
                CarritoProducto::COLUMNA_PRECIO,
                CarritoProducto::COLUMNA_COSTO,
            ])
            ->withTimestamps()
        ;
    }

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, self::COLUMNA_MESA_ID, Mesa::COLUMNA_ID);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, self::COLUMNA_CLIENTE_ID, Cliente::COLUMNA_ID);
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Producto::class, self::COLUMNA_PRODUCTO_DELIVERY_ID, Producto::COLUMNA_ID);
    }

    public function mozo(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, self::COLUMNA_MOZO_ID, Usuario::COLUMNA_ID);
    }

    public function setFechaCreacionAttribute($att)
    {
        if ($att instanceof CarbonImmutable || $att instanceof Carbon) {
            $this->attributes[self::COLUMNA_FECHA_CREACION] = $att->format('Y-m-d H:i:s');
        } else {
            $this->attributes[self::COLUMNA_FECHA_CREACION] = $att;
        }
    }

    public function getFechaCreacionAttribute(): ?CarbonImmutable
    {
        try {
            return CarbonImmutable::make($this->attributes[self::COLUMNA_FECHA_CREACION]);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getIsActivoAttribute()
    {
        return in_array($this->status, self::ESTADOS_ACTIVOS);
    }

    public function getProductoExistenteInCarrito($idProducto): ?Producto
    {
        return $this->productos->first(fn(Producto $p) => $p->id == $idProducto);
    }

    /**
     * @param $producto int|Producto
     * @return void
     */
    public function agregarProducto($producto, $cantidad = 1, $estado = CarritoProducto::ESTADO_PENDIENTE)
    {
        if (!$producto instanceof Producto) {
            $productoAgrega = Producto::findOrFail($producto);
        } else {
            $productoAgrega = $producto;
        }
        $this->productos()->attach($productoAgrega, [
            CarritoProducto::COLUMNA_COSTO => $productoAgrega->costo,
            CarritoProducto::COLUMNA_PRECIO => $productoAgrega->precio,
            CarritoProducto::COLUMNA_ESTADO => $estado,
            CarritoProducto::COLUMNA_CANTIDAD => $cantidad
        ]);
    }

}
