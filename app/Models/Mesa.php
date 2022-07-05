<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property Carrito $carritoActivo
 * @property mixed $code
 */
class Mesa extends ModelRoot
{
    const tableName = 'mesas';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_CODE = 'code';
    const COLUMNA_ACTIVO = 'activo';
    const COLUMNA_DESCRIPCION = 'descripcion';

    const RELACION_ULTIMO_CARITO = 'ultimoCarrito';
    const RELACION_CARRITO_ACTIVO = 'carritoActivo';

    protected $casts = [
        self::COLUMNA_ACTIVO => 'boolean'
    ];

    public function ultimoCarrito(): HasOne
    {
        return $this->hasOne(Carrito::class, Carrito::COLUMNA_MESA_ID, self::COLUMNA_ID)
            ->latestOfMany(Carrito::COLUMNA_ID);
    }

    public function carritoActivo(): HasOne
    {
        return $this->hasOne(Carrito::class, Carrito::COLUMNA_MESA_ID, self::COLUMNA_ID)
            ->where(Carrito::COLUMNA_STATUS, '<>',Carrito::ESTADO_FINALIZADO)
            ->latestOfMany(Carrito::COLUMNA_ID);
    }

    public function crearCarrito(?Cliente $cliente)
    {
        $carrito = new Carrito();
        $carrito->fecha_creacion = CarbonImmutable::now();
        $carrito->mesa()->associate($this);
        if ($cliente) {
            $carrito->cliente()->associate($cliente);
        }
        $carrito->save();
    }
}
