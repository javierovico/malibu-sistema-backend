<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $fecha_creacion
 * @see Carrito::setFechaCreacionAttribute()
 * @see Carrito::getFechaCreacionAttribute()
 */
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

    const ESTADO_FINALIZADO = 'finalizado';
    const ESTADO_CREADO = 'creado';

    const RELACION_MESA = 'mesa';
    const RELACION_CLIENTE = 'cliente';

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class, self::COLUMNA_MESA_ID, Mesa::COLUMNA_ID);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, self::COLUMNA_CLIENTE_ID, Cliente::COLUMNA_ID);
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
}
