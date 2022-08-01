<?php

use App\Models\Archivo;
use App\Models\Producto;
use App\Models\TipoProducto;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(Producto::CONNECTION_DB)->create(Producto::tableName, function (Blueprint $table) {
            $table->id(Producto::COLUMNA_ID);
            $table->foreignId(Producto::COLUMNA_TIPO_PRODUCTO_ID)->references(TipoProducto::COLUMNA_ID)->on(TipoProducto::tableName)->cascadeOnDelete();
            $table->foreignId(Producto::COLUMNA_ARCHIVO_ID)->nullable()->references(Archivo::COLUMNA_ID)->on(Archivo::tableName)->nullOnDelete();
            $table->string(Producto::COLUMNA_CODIGO, 100)->unique();
            $table->boolean(Producto::COLUMNA_STOCK)->index();
            $table->string(Producto::COLUMNA_NOMBRE, 200)->index();
            $table->text(Producto::COLUMNA_DESCRIPCION);
            $table->unsignedInteger(Producto::COLUMNA_PRECIO);
            $table->unsignedInteger(Producto::COLUMNA_COSTO);
            $table->string(Producto::COLUMNA_S3_KEY,250)->nullable();
            $table->softDeletes();
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
        Schema::connection(Producto::CONNECTION_DB)->dropIfExists(Producto::tableName);
    }
}
