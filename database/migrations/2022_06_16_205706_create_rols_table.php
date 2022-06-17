<?php

use App\Models\Rol;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(Rol::CONNECTION_DB)->create(Rol::tableName, function (Blueprint $table) {
            $table->id(Rol::COLUMNA_ID);
            $table->string(Rol::COLUMNA_CODIGO,100)->unique();
            $table->text(Rol::COLUMNA_DESCRIPCION);
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
        Schema::connection(Rol::CONNECTION_DB)->dropIfExists(Rol::tableName);
    }
}
