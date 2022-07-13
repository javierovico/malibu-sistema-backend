<?php

use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\Mesa;
use App\Models\Usuario;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarritosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(Carrito::CONNECTION_DB)->create(Carrito::tableName, function (Blueprint $table) {
            $table->id(Carrito::COLUMNA_ID);
            $table->foreignId(Carrito::COLUMNA_CLIENTE_ID)->nullable()->references(Cliente::COLUMNA_ID)->on(Cliente::tableName)->nullOnDelete();
            $table->foreignId(Carrito::COLUMNA_MOZO_ID)->references(Usuario::COLUMNA_ID)->on(Usuario::tableName);   // no se puede borrar el mozo
            $table->foreignId(Carrito::COLUMNA_MESA_ID)->nullable()->references(Mesa::COLUMNA_ID)->on(Mesa::tableName)->nullOnDelete();
            $table->string(Carrito::COLUMNA_STATUS, 30);
            $table->dateTime(Carrito::COLUMNA_FECHA_CREACION);
            $table->boolean(Carrito::COLUMNA_PAGADO);
            $table->boolean(Carrito::COLUMNA_IS_DELIVERY);
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
        Schema::connection(Carrito::CONNECTION_DB)->dropIfExists(Carrito::tableName);
    }
}
