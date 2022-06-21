<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function getProductos(Request $request)
    {
        $request->validate([
            'nombre' => 'max:20',
            'codigo' => '',
            'id' => '',
        ]);
        $nombre = $request->nombre;
        $codigo = $request->codigo;
        $id = $request->id;
        $query = Producto::query();
        if ($id) {
            $query->where(Producto::COLUMNA_ID,$id);
        } else {
            if ($codigo) {
                $query->where(Producto::COLUMNA_CODIGO,$codigo);
            }
            if ($nombre) {
                $query->where(Producto::COLUMNA_NOMBRE,'like', "%$nombre%");
            }
        }

        return paginate($query, $request);
    }
}
