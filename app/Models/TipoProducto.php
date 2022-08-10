<?php

namespace App\Models;


/**
 * @property mixed $code
 */
class TipoProducto extends ModelRoot
{
    const tableName = 'tipos_producto';
    protected $table = self::tableName;
    protected $primaryKey = self::COLUMNA_ID;

    const COLUMNA_ID = 'id';
    const COLUMNA_CODE = 'code';
    const COLUMNA_DESCRIPCION = 'descripcion';

    const TIPO_PRODUCTO_SIMPLE = 'simple';
    const TIPO_PRODUCTO_COMBO = 'combo';
    const TIPO_PRODUCTO_DELIVERY = 'delivery';

    /**
     * Describe los tipos de productos que son para el consumo (los que se deberian mostrar)
     * Nota: Solo delivery no esta formando parte
     */
    const TIPOS_PRODUCTO_CONSUMO = [
        self::TIPO_PRODUCTO_SIMPLE,
        self::TIPO_PRODUCTO_COMBO,
    ];

    const TIPOS_PRODUCTO = [
        self::TIPO_PRODUCTO_SIMPLE => [
            self::COLUMNA_DESCRIPCION => 'Producto simple'
        ],
        self::TIPO_PRODUCTO_COMBO => [
            self::COLUMNA_DESCRIPCION => 'Producto combo'
        ],
        self::TIPO_PRODUCTO_DELIVERY => [
            self::COLUMNA_DESCRIPCION => 'Precio Delivery'
        ]
    ];

    public static function getTipoProductoSimple(): self
    {
        return self::getTipoProductoByCode(self::TIPO_PRODUCTO_SIMPLE);
    }

    public static function getTipoProductoCombo(): self
    {
        return self::getTipoProductoByCode(self::TIPO_PRODUCTO_COMBO);
    }

    public static function getTipoProductoDelivery(): self
    {
        return self::getTipoProductoByCode(self::TIPO_PRODUCTO_DELIVERY);
    }

    public static function getTipoProductoByCode(string $code): self
    {
        return self::where(self::COLUMNA_CODE,$code)->firstOrFail();
    }
}
