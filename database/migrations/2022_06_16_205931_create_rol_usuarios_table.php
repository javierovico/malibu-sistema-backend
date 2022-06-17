<?php

use App\Models\Rol;
use App\Models\RolUsuario;
use App\Models\Usuario;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(RolUsuario::CONNECTION_DB)->create(RolUsuario::tableName, function (Blueprint $table) {
            $table->id(RolUsuario::COLUMNA_ID);
            $table->foreignId(RolUsuario::COLUMNA_ROL_ID)->references(Rol::COLUMNA_ID)->on(Rol::tableName)->cascadeOnDelete();
            $table->foreignId(RolUsuario::COLUMNA_USUARIO_ID)->references(Usuario::COLUMNA_ID)->on(Usuario::tableName)->cascadeOnDelete();
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
        Schema::connection(RolUsuario::CONNECTION_DB)->dropIfExists(RolUsuario::tableName);
    }
}
