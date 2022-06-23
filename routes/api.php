<?php

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
        Route::prefix('{Producto}')->group(function(){
            Route::get('',[ProductoController::class,'getProducto']);
            Route::put('',[ProductoController::class,'updateProducto']);
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
