<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
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
        $query->with(Producto::RELACION_IMAGEN);
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

    public function getProducto(Request $request, Producto $producto)
    {
        $producto->load(Producto::RELACION_IMAGEN);
        return self::respuestaDTOSimple('getProducto','Obtiene un producto por id','getProducto',[
            'producto' => $producto
        ]);
    }

    public function updateProducto(Request $request, Producto $producto)
    {
        if ($codigo = $request->get('codigo')) {
            $producto->codigo = $codigo;
        }
        if ($costo = $request->get('costo')) {
            $producto->costo = $costo;
        }
        if ($descripcion = $request->get('descripcion')) {
            $producto->descripcion = $descripcion;
        }
        if ($nombre = $request->get('nombre')) {
            $producto->nombre = $nombre;
        }
        if ($precio = $request->get('precio')) {
            $producto->precio = $precio;
        }
        if ($url = $request->get('url')) {
            $producto->asociarImagen64($url);
        }
        $producto->save();
        return self::respuestaDTOSimple('getProducto','Obtiene un producto por id','getProducto',[
            'producto' => $producto
        ]);
    }
}
