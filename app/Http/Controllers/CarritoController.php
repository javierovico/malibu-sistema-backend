<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    public function getStatusMesas(Request $request)
    {
        $request->validate([
            'withCarrito' => 'in:1,0',
            'withActivos' => 'in:1,0',
        ]);
        $mesasQuery = Mesa::query();
        if ($request->get('withCarrito')) {
            $mesasQuery->with(Mesa::RELACION_CARRITO_ACTIVO);
        }
        if (null !== ($withActivos = $request->get('withActivos'))) {
            $mesasQuery->where(Mesa::COLUMNA_ACTIVO, '=',$withActivos?'1':'0');
        }
        $mesas = $mesasQuery->get();
        return self::respuestaDTOSimple('getStatusMesas','Obtiene las mesas y su estado', 'getStatusMesas',[
            'mesas' => $mesas
        ]);
    }
}
