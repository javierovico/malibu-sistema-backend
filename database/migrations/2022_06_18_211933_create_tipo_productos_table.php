<?php

use App\Models\TipoProducto;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(TipoProducto::CONNECTION_DB)->create(TipoProducto::tableName, function (Blueprint $table) {
            $table->id(TipoProducto::COLUMNA_ID);
            $table->string(TipoProducto::COLUMNA_CODE,50);
            $table->text(TipoProducto::COLUMNA_DESCRIPCION);
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
        Schema::connection(TipoProducto::CONNECTION_DB)->dropIfExists(TipoProducto::tableName);
    }
}
