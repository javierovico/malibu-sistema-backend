<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

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
}
