<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\TipoProducto;
use Illuminate\Database\Seeder;

class ProductoDeliverySeeder extends Seeder
{
    public const PRECIO_DELIVERY = [
        [
            Producto::COLUMNA_PRECIO => 5000,
            Producto::COLUMNA_COSTO => 5000,
            Producto::COLUMNA_NOMBRE => 'Delivery 5km',
            Producto::COLUMNA_CODIGO => 'del_5000',
            Producto::COLUMNA_DESCRIPCION => 'Delivery hasta 5km',
        ],
        [
            Producto::COLUMNA_PRECIO => 10000,
            Producto::COLUMNA_COSTO => 10000,
            Producto::COLUMNA_NOMBRE => 'Delivery 10km',
            Producto::COLUMNA_CODIGO => 'del_10000',
            Producto::COLUMNA_DESCRIPCION => 'Delivery hasta 10km',
        ],
        [
            Producto::COLUMNA_PRECIO => 15000,
            Producto::COLUMNA_COSTO => 15000,
            Producto::COLUMNA_NOMBRE => 'Delivery 15km',
            Producto::COLUMNA_CODIGO => 'del_15000',
            Producto::COLUMNA_DESCRIPCION => 'Delivery hasta 15km',
        ],
        [
            Producto::COLUMNA_PRECIO => 20000,
            Producto::COLUMNA_COSTO => 20000,
            Producto::COLUMNA_NOMBRE => 'Delivery 20km',
            Producto::COLUMNA_CODIGO => 'del_20000',
            Producto::COLUMNA_DESCRIPCION => 'Delivery hasta 20km',
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipoProductoDeliver = TipoProducto::getTipoProductoDelivery();
        foreach (self::PRECIO_DELIVERY as $item) {
            $producto = new Producto($item);
            $producto->tipoProducto()->associate($tipoProductoDeliver);
            $producto->save();
        }
    }
}
