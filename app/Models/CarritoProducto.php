<?php

namespace App\Models;

use App\Exceptions\ExceptionCarritoProductoState;
use App\Exceptions\ExceptionSystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response;

/**
 * @property mixed $estado
 * @see CarritoProducto::setEstadoAttribute()
 * @property mixed $calculado
 * @see CarritoProducto::getCalculadoAttribute()
 * @property boolean $isActivo
 * @see CarritoProducto::getIsActivoAttribute()
 * @property integer|false $posicionEstado
 * @see CarritoProducto::getPosicionEstadoAttribute()
 */
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

    protected $guarded = [];

    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_PREPARACION = 'preparacion';
    const ESTADO_FINALIZADO = 'finalizado';

    const ESTADOS_ADMITIDOS_ORDEN = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_PREPARACION,
        self::ESTADO_FINALIZADO,
    ];

    const ESTADOS_ACTIVOS = [
        self::ESTADO_PREPARACION,
    ];

    /**
     * Retorna la posicion del estado en el array ESTADOS_ADMITIDOS_ORDEN
     * @param $att
     * @return false|int
     */
    private static function calculePosicionEstado($att)
    {
        return array_search($att, self::ESTADOS_ADMITIDOS_ORDEN);
    }

    public function getIsActivoAttribute(): bool
    {
        return in_array($this->estado, self::ESTADOS_ACTIVOS);
    }

    public function getPosicionEstadoAttribute()
    {
        return array_key_exists(self::COLUMNA_ESTADO,$this->attributes)?self::calculePosicionEstado($this->attributes[self::COLUMNA_ESTADO]):false;
    }

    /**
     * @throws ExceptionSystem
     * @throws ExceptionCarritoProductoState
     */
    public function setEstadoAttribute($att)
    {
        if (!in_array($att,self::ESTADOS_ADMITIDOS_ORDEN)) {
            throw ExceptionCarritoProductoState::makeEstadoNoAdmitido($att);
//            throw ExceptionSystem::createException('Estado `' . $att . '` no esta en la lista de estados admitidos','estadoNoAdmitido','Estado no admitido', Response::HTTP_NOT_ACCEPTABLE);
        }
        $nuevaPosicion = self::calculePosicionEstado($att);
        if ($this->posicionEstado!==false && $this->posicionEstado > $nuevaPosicion) {
            throw ExceptionCarritoProductoState::makeEstadoNoRetroceso($this->estado,$att);
//            throw ExceptionSystem::createException("No se puede retroceder del estado `$this->estado`($this->posicionEstado) al estado `$att` ($nuevaPosicion)",'estadoNoRetroceso','Estado no retroceso', Response::HTTP_NOT_ACCEPTABLE);
        }
        $this->attributes[self::COLUMNA_ESTADO] = $att;
    }
}
