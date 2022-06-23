<?php

use App\Models\Archivo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(Archivo::CONNECTION_DB)->create(Archivo::tableName, function (Blueprint $table) {
            $table->id(Archivo::COLUMNA_ID);
            $table->string(Archivo::COLUMNA_PATH);
            $table->tinyInteger(Archivo::COLUMNA_TIPO);
            $table->unique([Archivo::COLUMNA_PATH,Archivo::COLUMNA_TIPO]);
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
        Schema::connection(Archivo::CONNECTION_DB)->dropIfExists(Archivo::tableName);
    }
}
