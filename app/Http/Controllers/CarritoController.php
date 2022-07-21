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
            $mesasQuery->where(Mesa::COLUMNA_ACTIVO, '=', $activo ? '1' : '0');
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
            'clienteId' => 'exists:' . Cliente::class . ',' . Cliente::COLUMNA_ID,
        ]);
        if ($mesa->carritoActivo) {
            throw ExceptionSystem::createException('La mesa ' . $mesa->code . ' ya esta asignada', 'errorAsignacion', 'Mesa en uso', Response::HTTP_NOT_ACCEPTABLE);
        }
        /** @var Cliente $cliente */
        $cliente = ($clienteId = $request->get('clienteId')) ? Cliente::find($clienteId) : null;
        $carrito = $mesa->nuevoCarrito($cliente, $request->user());
        $carrito->save();
        CarritoEvent::dispatch($carrito);
        return self::respuestaDTOSimple('asignarMesa', 'Asigna una mesa a un cliente', 'asignarMesa');
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
            $loads[] = Carrito::RELACION_PRODUCTOS . '.' . Producto::RELACION_TIPO_PRODUCTO;
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
            $query->whereIn(Carrito::COLUMNA_STATUS, Carrito::ESTADOS_ACTIVOS);
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
        return self::respuestaDTOSimple('createCarrito', 'Crea un carrito', 'createCarrito', [
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
            'is_delivery' => 'in:1,0',
            'pagado' => 'in:1,0',
            'cambiosEstados' => 'array',
            'cambiosEstados.*.id' => 'numeric|exists:' . Producto::class . ',' . Producto::COLUMNA_ID,
            'cambiosEstados.*.estado' => 'in:' . join(',',CarritoProducto::ESTADOS_ADMITIDOS_ORDEN)
        ]);
        if (!$carrito->isActivo) {
            throw ExceptionSystem::createException('El carrito ya no esta disponible para su modificacion', 'carritoNoDispo', 'Carrito no disponible', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $previamentePagado = $carrito->pagado;
        if ($request->has('pagado')) {
            $carrito->pagado = !!$request->get('pagado');
        }
        if ($request->has('mesaId')) {      //si se tiene la mesa se prepara para agregar o quitar
            $mesa = ($mesaId = $request->get('mesaId')) ? Mesa::findOrFail($mesaId) : null;
            if ($mesa && $mesa->carritoActivo) {
                throw ExceptionSystem::createExceptionInput('mesaId', ['La mesa ' . $mesa->code . ' ya esta asignada']);
            }
            if ($mesa) {
                $carrito->mesa()->associate($mesa);
            } else {
                $carrito->mesa()->dissociate();
            }
        }
        if ($request->has('clienteId')) {      //si se tiene la cliente se prepara para agregar o quitar
            if ($previamentePagado) {
                throw ExceptionSystem::createExceptionInput('clienteId',['Ya no se pueden modificar luego de haber pagado']);
            }
            $cliente = ($clienteId = $request->get('clienteId')) ? Cliente::findOrFail($clienteId) : null;
            if ($cliente) {
                $carrito->cliente()->associate($cliente);
            } else {
                $carrito->cliente()->dissociate();
            }
        }
        if ($request->has('is_delivery')) {
            if ($previamentePagado) {
                throw ExceptionSystem::createExceptionInput('is_delivery',['Ya no se pueden modificar luego de haber pagado']);
            }
            $carrito->is_delivery = !!$request->get('is_delivery');
        }
        if ($carrito->fresh()) {  //si ya existia
            if ($carrito->pagado) {
                $carrito->status = Carrito::ESTADO_PAGADO;
            } else {
                $carrito->status = Carrito::ESTADO_MODIFICADO;
            }
        }
        //Antes de agregar los productos nos aseguramos que el carrito este guardado
        $carrito->save();
        if (($productosIdQuita = $request->get('productosIdQuita')) && count($productosIdQuita)) {
            if ($previamentePagado) {
                throw ExceptionSystem::createExceptionInput('productosIdQuita',['Ya no se pueden modificar luego de haber pagado']);
            }
            $carrito->load(Carrito::RELACION_PRODUCTOS);    //carga todos los productos
            foreach ($productosIdQuita as $idQuita) {
                $producto = $carrito->getProductoExistenteInCarrito($idQuita);
                $carritoProducto = $producto->carritoProducto;
                if ($carritoProducto->isActivo) {
                    throw ExceptionSystem::createExceptionInput('productos',['Id: ' . $idQuita . ' ya no esta en estado ' . CarritoProducto::ESTADO_INICIADO . ', no se puede quitar']);
                } else {
                    $carrito->productos()->detach($idQuita);
                }
            }
        }
        if (($productosIdAgrega = $request->get('productosIdAgrega')) && count($productosIdAgrega)) {
            if ($previamentePagado) {
                throw ExceptionSystem::createExceptionInput('productosIdAgrega',['Ya no se pueden modificar luego de haber pagado']);
            }
            foreach ($productosIdAgrega as $idAgrega) {
                $productoAgrega = Producto::findOrFail($idAgrega);
                $carrito->productos()->attach($productoAgrega, [
                    CarritoProducto::COLUMNA_COSTO => $productoAgrega->costo,
                    CarritoProducto::COLUMNA_PRECIO => $productoAgrega->precio,
                    CarritoProducto::COLUMNA_ESTADO => CarritoProducto::ESTADO_INICIADO
                ]);
            }
        }
        if ($cambiosEstados = $request->get('cambiosEstados')) {
            $carrito->load(Carrito::RELACION_PRODUCTOS);    //carga todos los productos
            foreach ($cambiosEstados as $ce) {
                $producto = $carrito->getProductoExistenteInCarrito($ce['id']);
                $carritoProducto = $producto->carritoProducto;
                try {
                    $carritoProducto->estado = $ce['estado'];
                } catch (\Throwable $e) {

                }
                $carritoProducto->save();
            }
        }
        self::cargarRelaciones($request, $carrito);
        CarritoEvent::dispatch($carrito);
        if ($pasaMano) {
            return $carrito;
        } else {
            return self::respuestaDTOSimple('updateCarrito', 'Modifica un carrito', 'updateCarrito', [
                'carrito' => $carrito
            ]);
        }
    }
}
