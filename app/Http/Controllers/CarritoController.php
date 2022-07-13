<?php

namespace App\Http\Controllers;

use App\Events\CarritoEvent;
use App\Exceptions\ExceptionSystem;
use App\Models\Carrito;
use App\Models\CarritoProducto;
use App\Models\Cliente;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CarritoController extends Controller
{
    public function getStatusMesas(Request $request)
    {
        $request->validate([
            'withCarrito' => 'in:1,0',
            'withCarritoProductos' => 'in:1,0',
            'activo' => 'in:1,0',
            'withMozo' => 'in:1,0',
            'withCliente' => 'in:1,0'
        ]);
        $mesasQuery = Mesa::query();
        if ($request->get('withCarrito')) {
            $mesasQuery->with(Mesa::RELACION_CARRITO_ACTIVO);
        }
        if ($request->get('withMozo')) {
            $mesasQuery->with(Mesa::RELACION_CARRITO_ACTIVO . '.' . Carrito::RELACION_MOZO);
        }
        if ($request->get('withCarritoProductos')) {
            $mesasQuery->with(Mesa::RELACION_CARRITO_ACTIVO . '.' . Carrito::RELACION_PRODUCTOS);
        }
        if ($request->get('withCliente')) {
            $mesasQuery->with(Mesa::RELACION_CARRITO_ACTIVO . '.' . Carrito::RELACION_CLIENTE);
        }
        if (null !== ($activo = $request->get('activo'))) {
            $mesasQuery->where(Mesa::COLUMNA_ACTIVO, '=',$activo?'1':'0');
        }
        return paginate($mesasQuery, $request);
    }

    /**
     * @throws ExceptionSystem
     * @deprecated ya no usar
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
        $carrito = $mesa->nuevoCarrito($cliente,$request->user());
        $carrito->save();
        CarritoEvent::dispatch($carrito);
        return self::respuestaDTOSimple('asignarMesa','Asigna una mesa a un cliente','asignarMesa');
    }

    /**
     * @param Request $request
     * @param Builder|Carrito $query
     * @return void
     */
    private static function cargarRelaciones(Request $request, $query)
    {
        $request->validate([
            'withCliente' => 'in:1,0',
            'withMozo' => 'in:1,0',
            'withProductos' => 'in:1,0',
            'withMesa' => 'in:1,0',
        ]);
        $loads = [];
        if ($request->get('withCliente')) {
            $loads[] = Carrito::RELACION_CLIENTE;
        }
        if ($request->get('withMozo')) {
            $loads[] = Carrito::RELACION_MOZO;
        }
        if ($request->get('withProductos')) {
            $loads[] = Carrito::RELACION_PRODUCTOS;
        }
        if ($request->get('withMesa')) {
            $loads[] = Carrito::RELACION_MESA;
        }
        if ($query instanceof Builder) {
            $query->with($loads);
        } else if ($query instanceof Carrito) {
            $query->load($loads);
        }
    }

    public function getCarritos(Request $request)
    {
        $request->validate([
            'soloActivos' => 'in:1,0',
        ]);
        $query = Carrito::query();
        if ($request->get('soloActivos')) {
            $query->whereIn(Carrito::COLUMNA_STATUS,Carrito::ESTADOS_ACTIVOS);
        }
        self::cargarRelaciones($request, $query);
        return paginate($query, $request);
    }

    /**
     * @throws ExceptionSystem
     */
    public function createCarrito(Request $request)
    {
        $request->validate([
        ]);
        /** @var Usuario $user */
        $user = $request->user();
        $carrito = $this->updateCarrito($request, Carrito::nuevoCarrito($user), true);
        return self::respuestaDTOSimple('createCarrito','Crea un carrito','createCarrito',[
            'carrito' => $carrito
        ]);
    }

    /**
     * @throws ExceptionSystem
     */
    public function updateCarrito(Request $request, Carrito $carrito, $pasaMano = false)
    {
        $request->validate([
            'productosIdAgrega' => 'array',
            'productosIdAgrega.*' => 'numeric|exists:' . Producto::class . ',' . Producto::COLUMNA_ID,
            'productosIdQuita' => 'array',
            'productosIdQuita.*' => 'numeric|exists:' . Producto::class . ',' . Producto::COLUMNA_ID,
            'mesaId' => 'nullable|numeric|exists:' . Mesa::class . ',' . Mesa::COLUMNA_ID,
            'clienteId' => 'nullable|numeric|exists:' . Mesa::class . ',' . Mesa::COLUMNA_ID,
        ]);
        if (!$carrito->isActivo) {
            throw ExceptionSystem::createException('El carrito ya no esta disponible para su modificacion','carritoNoDispo','Carrito no disponible',Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if ($request->has('mesaId')) {      //si se tiene la mesa se prepara para agregar o quitar
            $mesa = ($mesaId = $request->get('mesaId')) ? Mesa::findOrFail($mesaId) : null;
            if ($mesa && $mesa->carritoActivo) {
                throw ExceptionSystem::createException('La mesa ' . $mesa->code . ' ya esta asignada','errorAsignacion','Mesa en uso',Response::HTTP_NOT_ACCEPTABLE);
            }
            if ($mesa) {
                $carrito->mesa()->associate($mesa);
            } else {
                $carrito->mesa()->dissociate();
            }
        }
        if ($request->has('clienteId')) {      //si se tiene la cliente se prepara para agregar o quitar
            $cliente = ($clienteId = $request->get('clienteId')) ? Cliente::findOrFail($clienteId) : null;
            if ($cliente) {
                $carrito->cliente()->associate($cliente);
            } else {
                $carrito->cliente()->dissociate();
            }
        }
        if ($carrito->fresh()) {  //si ya existia
            $carrito->status = Carrito::ESTADO_EDITADO;
        }
        //Antes de agregar los productos nos aseguramos que el carrito este guardado
        $carrito->save();
        if ($productosIdAgrega = $request->get('productosIdAgrega')) {
            foreach($productosIdAgrega as $idAgrega) {
                $productoAgrega = Producto::findOrFail($idAgrega);
                $carrito->productos()->attach($productoAgrega,[
                    CarritoProducto::COLUMNA_COSTO => $productoAgrega->costo,
                    CarritoProducto::COLUMNA_PRECIO => $productoAgrega->precio,
                    CarritoProducto::COLUMNA_ESTADO => CarritoProducto::ESTADO_PREPARACION
                ]);
            }
        }
        if ($productosIdQuita = $request->get('productosIdQuita')) {
            $carrito->productos()->detach($productosIdQuita);
        }
        self::cargarRelaciones($request, $carrito);
        CarritoEvent::dispatch($carrito);
        if ($pasaMano) {
            return $carrito;
        } else {
            return self::respuestaDTOSimple('updateCarrito','Modifica un carrito','updateCarrito',[
                'carrito' => $carrito
            ]);
        }
    }
}
