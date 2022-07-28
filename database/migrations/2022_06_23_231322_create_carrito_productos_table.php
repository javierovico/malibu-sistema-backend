<?php

use App\Models\Carrito;
use App\Models\CarritoProducto;
use App\Models\Producto;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarritoProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(CarritoProducto::CONNECTION_DB)->create(CarritoProducto::tableName, function (Blueprint $table) {
            $table->id(CarritoProducto::COLUMNA_ID);
            $table->foreignId(CarritoProducto::COLUMNA_CARRITO_ID)->references(Carrito::COLUMNA_ID)->on(Carrito::tableName)->cascadeOnDelete();
            $table->foreignId(CarritoProducto::COLUMNA_PRODUCTO_ID)->references(Producto::COLUMNA_ID)->on(Producto::tableName)->cascadeOnDelete();
            $table->tinyInteger(CarritoProducto::COLUMNA_CANTIDAD);
            $table->string(CarritoProducto::COLUMNA_ESTADO,50);
            $table->unsignedInteger(CarritoProducto::COLUMNA_PRECIO);
            $table->unsignedInteger(CarritoProducto::COLUMNA_COSTO);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(CarritoProducto::CONNECTION_DB)->dropIfExists(CarritoProducto::tableName);
    }
}
