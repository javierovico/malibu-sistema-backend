<?php

use App\Models\Archivo;
use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(Cliente::CONNECTION_DB)->create(Cliente::tableName, function (Blueprint $table) {
            $table->id(Cliente::COLUMNA_ID);
            $table->foreignId(Cliente::COLUMNA_USUARIO_ID)->nullable()->references(Usuario::COLUMNA_ID)->on(Usuario::tableName)->nullOnDelete();
            $table->foreignId(Cliente::COLUMNA_IMAGEN_ID)->nullable()->references(Archivo::COLUMNA_ID)->on(Archivo::tableName)->nullOnDelete();
            $table->string(Cliente::COLUMNA_NOMBRE, 100);
            $table->string(Cliente::COLUMNA_RUC, 30)->nullable();
            $table->string(Cliente::COLUMNA_TELEFONO, 20)->nullable()->unique();
            $table->string(Cliente::COLUMNA_CIUDAD, 50)->nullable();
            $table->string(Cliente::COLUMNA_BARRIO, 50)->nullable();
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
        Schema::connection(Cliente::CONNECTION_DB)->dropIfExists(Cliente::tableName);
    }
}
