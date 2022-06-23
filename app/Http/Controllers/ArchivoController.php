<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchivoController extends Controller
{
    public function getImagen(Request $request, $nombre){
        $contenido = Storage::disk('uploadPrueba')->get($nombre);
        return response($contenido,200,['Content-Type' => 'image']);
    }
}
