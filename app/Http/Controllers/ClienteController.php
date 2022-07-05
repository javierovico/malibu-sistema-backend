<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    const RELACIONES_BASICAS = [
        Cliente::RELACION_IMAGEN,
    ];

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
        $query->with(self::RELACIONES_BASICAS);
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
            if (is_array($ciudad)) {
                $query->whereIn(Cliente::COLUMNA_CIUDAD,$ciudad);
            } else {
                $query->where(Cliente::COLUMNA_CIUDAD,'like', '%' . $ciudad . '%');
            }
        }

        return paginate($query, $request);
    }

    public function createCliente(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
        ]);
        $cliente = $this->updateCliente($request, new Cliente(), true);
        return self::respuestaDTOSimple('addCliente','Crea nuevo cliente','addCliente',[
            'cliente' => $cliente
        ]);
    }

    public function updateCliente(Request $request, Cliente $cliente, $pasaMano = false)
    {
        $request->validate([
            'nombre' => 'min:2,max:100',
            'base64Image' => 'regex:#^data:image/\w+;base64,#i|nullable',
            'deleteImage' => 'in:si,no',
            'ruc' => 'max:30',
            'telefono' => 'max:20',
            'ciudad' => 'max:50',
            'barrio' => 'max:50',
        ]);
        if ($nombre = $request->get('nombre')) {
            $cliente->nombre = $nombre;
        }
        if ($ruc = $request->get('ruc')) {
            $cliente->ruc = $ruc;
        }
        if ($telefono = $request->get('telefono')) {
            $cliente->telefono = $telefono;
        }
        if ($ciudad = $request->get('ciudad')) {
            $cliente->ciudad = $ciudad;
        }
        if ($barrio = $request->get('barrio')) {
            $cliente->barrio = $barrio;
        }
        if ($base64Image = $request->get('base64Image')) {
            $cliente->asociarImagen64($base64Image);
        } else if ($request->get('deleteImage','no') == 'si') {
            $cliente->borrarImagen();
        }
        $cliente->save();
        $cliente->load(self::RELACIONES_BASICAS);
        if ($pasaMano) {
            return $cliente;
        } else {
            return self::respuestaDTOSimple('updateCliente','Modifica un cliente','updateCliente',[
                'cliente' => $cliente
            ]);
        }
    }

    public function deleteCliente(Request $request, Cliente $cliente)
    {
        $cliente->delete();
        return self::respuestaDTOSimple('deleteCliente','Borra un cliente','deleteCliente');
    }
}
