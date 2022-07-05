<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Producto;
use App\Models\TipoProducto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    const RELACIONES_BASICAS = [
        Producto::RELACION_IMAGEN,
        Producto::RELACION_TIPO_PRODUCTO,
        Producto::RELACION_PRODUCTO_COMBOS . '.' . Producto::RELACION_IMAGEN,
        Producto::RELACION_PRODUCTO_COMBOS . '.' . Producto::RELACION_TIPO_PRODUCTO
    ];

    public function getProductos(Request $request)
    {
        $request->validate([
            'nombre' => 'max:20',
            'codigo' => '',
            'id' => '',
            'tiposProducto' => 'array',
            'sortByList' => 'json',
        ]);
        $nombre = $request->nombre;
        $codigo = $request->codigo;
        $id = $request->id;
        $query = Producto::query();
        $query->with(self::RELACIONES_BASICAS);
        if ($tiposProducto = $request->get('tiposProducto')) {
            $query->whereHas(Producto::RELACION_TIPO_PRODUCTO,fn(Builder $q)=>$q->whereIn(TipoProducto::COLUMNA_CODE,$tiposProducto));
        }
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
        //ordenamiento
        if (($ordenes = $request->get('sortByList')) && ($ordenes = json_decode($ordenes,true)) && is_array($ordenes)) {
            if (array_key_exists('tipoProducto',$ordenes) && ($item = $ordenes['tipoProducto'])) {
                $query->orderBy(Producto::COLUMNA_TIPO_PRODUCTO, $item == 'descend' ? 'desc' : 'asc');
            }
            if (array_key_exists('precio',$ordenes) && ($item = $ordenes['precio'])) {
                $query->orderBy(Producto::COLUMNA_PRECIO, $item == 'descend' ? 'desc' : 'asc');
            }
            if (array_key_exists('costo',$ordenes) && ($item = $ordenes['costo'])) {
                $query->orderBy(Producto::COLUMNA_COSTO, $item == 'descend' ? 'desc' : 'asc');
            }
            if (array_key_exists('nombre',$ordenes) && ($item = $ordenes['nombre'])) {
                $query->orderBy(Producto::COLUMNA_NOMBRE, $item == 'descend' ? 'desc' : 'asc');
            }
            if (array_key_exists('codigo',$ordenes) && ($item = $ordenes['codigo'])) {
                $query->orderBy(Producto::COLUMNA_CODIGO, $item == 'descend' ? 'desc' : 'asc');
            }
            if (array_key_exists('id',$ordenes) && ($item = $ordenes['id'])) {
                $query->orderBy(Producto::COLUMNA_ID, $item == 'descend' ? 'desc' : 'asc');
            }
        }

        return paginate($query, $request);
    }

    public function getProducto(Request $request, Producto $producto)
    {
        $producto->load(self::RELACIONES_BASICAS);
        return self::respuestaDTOSimple('getProducto','Obtiene un producto por id','getProducto',[
            'producto' => $producto
        ]);
    }

    public function addProducto(Request $request)
    {
        $request->validate([
            'costo' => 'required',
            'precio' => 'required',
            'codigo' => 'required',
            'nombre' => 'required',
        ]);
        $producto = $this->updateProducto($request, new Producto(), true);
        return self::respuestaDTOSimple('addProducto','Crea nuevo producto','addProducto',[
            'producto' => $producto
        ]);
    }

    public function updateProducto(Request $request, Producto $producto, $pasaMano = false)
    {
        $request->validate([
            'costo' => 'numeric|min:1',
            'precio' => 'numeric|min:1',
            'codigo' => 'max:100',
            'nombre' => 'max:200',
            'base64Image' => 'regex:#^data:image/\w+;base64,#i|nullable',
            'deleteImage' => 'in:si,no',
            'tipoProducto' => 'exists:'.TipoProducto::class.',' .TipoProducto::COLUMNA_CODE,
            'combos' => 'array',
            'combos.*' => 'numeric|exists:' . Producto::class . ',' . Producto::COLUMNA_ID,
        ]);
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
        if ($base64Image = $request->get('base64Image')) {
            $producto->asociarImagen64($base64Image);
        } else if ($request->get('deleteImage','no') == 'si') {
            $producto->borrarImagen();
        }
        if ($tipoProductoCode = $request->get('tipoProducto')) {
            $tipoProducto = TipoProducto::getTipoProductoByCode($tipoProductoCode);
            $producto->tipoProducto()->associate($tipoProducto);
        }
        $producto->save();
        //Esta despues porque se requiere que el producto exista
        if (is_array($combosIds = $request->get('combos'))) {
            $producto->productoCombos()->sync($combosIds);
        }
        $producto->load(self::RELACIONES_BASICAS);
        if ($pasaMano) {
            return $producto;
        } else {
            return self::respuestaDTOSimple('updateProducto','Obtiene un producto por id','updateProducto',[
                'producto' => $producto
            ]);
        }
    }

    public function deleteProducto(Request $request, Producto $producto)
    {
        $producto->delete();
        return self::respuestaDTOSimple('deleteProducto','Borra un producto','deleteProducto');
    }
}
