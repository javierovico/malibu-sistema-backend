<?php

use App\Models\ComboProducto;
use App\Models\Producto;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComboProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(ComboProducto::CONNECTION_DB)->create(ComboProducto::tableName, function (Blueprint $table) {
            $table->id(ComboProducto::COLUMNA_ID);
            $table->foreignId(ComboProducto::COLUMNA_COMBO_ID)->references(Producto::COLUMNA_ID)->on(Producto::tableName)->cascadeOnDelete();
            $table->foreignId(ComboProducto::COLUMNA_PRODUCTO_ID)->references(Producto::COLUMNA_ID)->on(Producto::tableName)->cascadeOnDelete();
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
        Schema::connection(ComboProducto::CONNECTION_DB)->dropIfExists(ComboProducto::tableName);
    }
}
