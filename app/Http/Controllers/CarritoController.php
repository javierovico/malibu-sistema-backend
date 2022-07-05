<?php

namespace App\Http\Controllers;

use App\Exceptions\ExceptionSystem;
use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CarritoController extends Controller
{
    public function getStatusMesas(Request $request)
    {
        $request->validate([
            'withCarrito' => 'in:1,0',
            'activo' => 'in:1,0',
        ]);
        $mesasQuery = Mesa::query();
        if ($request->get('withCarrito')) {
            $mesasQuery->with(Mesa::RELACION_CARRITO_ACTIVO);
        }
        if (null !== ($activo = $request->get('activo'))) {
            $mesasQuery->where(Mesa::COLUMNA_ACTIVO, '=',$activo?'1':'0');
        }
        return paginate($mesasQuery, $request);
    }

    /**
     * @throws ExceptionSystem
     */
    public function asignarMesa(Request $request, Mesa $mesa)
    {
        $request->validate([
            'clienteId' => 'exists:'.Cliente::class.',' .Cliente::COLUMNA_ID,
        ]);
        if ($mesa->carritoActivo) {
            throw ExceptionSystem::createException('La mesa ' . $mesa->code . ' ya esta asignada','errorAsignacion','Mesa en uso',Response::HTTP_NOT_ACCEPTABLE);
        }
        /** @var Cliente $cliente */
        $cliente = ($clienteId = $request->get('clienteId')) ? Cliente::find($clienteId) : null;
        $mesa->crearCarrito($cliente);
        return self::respuestaDTOSimple('asignarMesa','Asigna una mesa a un cliente','asignarMesa');
    }
}
