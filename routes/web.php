<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/', function () {
    return json_encode([
        'nombre' => config('app.name'),
        'env' => config('app.env'),
        'debug' => config('app.debug'),
        'url' => config('app.url'),
        'ip' => $_SERVER['SERVER_ADDR'],
        'user' => exec('whoami'),
    ], JSON_PRETTY_PRINT);
});

