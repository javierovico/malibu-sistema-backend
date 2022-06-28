<?php

use App\Events\MessageEvent;
use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware([AuthMiddleware::class])->group(function () {
    Route::prefix('auth')->group(function(){
        Route::post('login', [UserController::class, 'login'])->withoutMiddleware(AuthMiddleware::class);
        Route::middleware([AuthMiddleware::class])->group(function () {
            Route::get('/user', [UserController::class, 'getUser']);
            Route::get('/logout', [UserController::class, 'logout']);
        });
    });
    Route::prefix('producto')->group(function(){
        Route::get('',[ProductoController::class, 'getProductos']);
        Route::post('',[ProductoController::class, 'addProducto']);
        Route::prefix('{Producto}')->group(function(){
            Route::get('',[ProductoController::class,'getProducto']);
            Route::put('',[ProductoController::class,'updateProducto']);
            Route::delete('',[ProductoController::class,'deleteProducto']);
        });
    });
    Route::prefix('archivo')->group(function(){
        Route::prefix('imagen')->group(function(){
            Route::prefix('{archivoPath}')->group(function(){
                Route::get('',[ArchivoController::class,'getImagen'])->withoutMiddleware(AuthMiddleware::class);
            });
        });
    });
});

Route::any('new-message', function (Request $request) {
    $request->validate([
        'user' => 'required',
        'message' => 'required',
        'private' => 'in:1,0'
    ]);
    event(new MessageEvent($request->get('user'), $request->get('message'),$request->get('private')));
    return 'ok';
});

Route::any('new-privado', function (Request $request) {
    event(new MessageEvent("aldo", "chau", true));
    return 'ok';
});
