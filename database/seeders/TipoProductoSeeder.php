<?php

namespace Database\Seeders;

use App\Models\TipoProducto;
use Illuminate\Database\Seeder;

class TipoProductoSeeder extends Seeder
{

    public function run()
    {
        foreach (TipoProducto::TIPOS_PRODUCTO as $code=>$item) {
            $tipoProducto = new TipoProducto($item);
            $tipoProducto->code = $code;
            $tipoProducto->save();
        }
    }
}
