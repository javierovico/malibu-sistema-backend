<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function getClientes(Request $request)
    {
        $request->validate([
            'nombre' => 'max:30',
            'ruc' => '',
            'id' => '',
            'telefono' => '',
            'barrio' => '',
            'ciudad' => ''
        ]);
        $query = Cliente::query();
        if ($id = $request->get('id')) {
            $query->where(Cliente::COLUMNA_ID,$id);
        }
        if ($nombre = $request->get('nombre')) {
            $query->where(Cliente::COLUMNA_NOMBRE,'like','%' . $nombre . '%');
        }
        if ($ruc = $request->get('ruc')) {
            $query->where(Cliente::COLUMNA_RUC,'like','%' . $ruc . '%');
        }
        if ($telefono = $request->get('telefono')) {
            $query->where(Cliente::COLUMNA_TELEFONO,'like', '%' . $telefono . '%');
        }
        if ($barrio = $request->get('barrio')) {
            $query->where(Cliente::COLUMNA_BARRIO,'like', '%' . $barrio . '%');
        }
        if ($ciudad = $request->get('ciudad')) {
            $query->where(Cliente::COLUMNA_CIUDAD,'like', '%' . $ciudad . '%');
        }

        return paginate($query, $request);
    }
}
