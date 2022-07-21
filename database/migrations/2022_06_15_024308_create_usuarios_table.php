<?php

use App\Models\Usuario;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(Usuario::CONNECTION_DB)->create(Usuario::tableName, function (Blueprint $table) {
            $table->id(Usuario::COLUMNA_ID);
            $table->string(Usuario::COLUMNA_USER,100)->unique();
            $table->string(Usuario::COLUMNA_PASSWORD,250);
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
        Schema::connection(Usuario::CONNECTION_DB)->dropIfExists(Usuario::tableName);
    }
}
