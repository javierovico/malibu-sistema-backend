<?php

use App\Models\Mesa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMesasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(Mesa::CONNECTION_DB)->create(Mesa::tableName, function (Blueprint $table) {
            $table->id(Mesa::COLUMNA_ID);
            $table->string(Mesa::COLUMNA_CODE,20);
            $table->boolean(Mesa::COLUMNA_ACTIVO);
            $table->text(Mesa::COLUMNA_DESCRIPCION);
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
        Schema::connection(Mesa::CONNECTION_DB)->dropIfExists(Mesa::tableName);
    }
}
