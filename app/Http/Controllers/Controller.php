<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public static function respuestaDTOSimple($title, $message, $code = 'none', $data = [], $success = true)
    {
        return self::respuestaDTO($title, $message, $code, $data, $success ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    public static function respuestaDTONotFound($message)
    {
        return self::respuestaDTO('Not Found', $message, 'not found', null,Response::HTTP_NOT_FOUND );
    }

    public static function respuestaDTO($title, $message, $code = 'none', $data = [], $status = Response::HTTP_OK)
    {
        return response([
            'title' => $title,
            'message' => $message,
            'code' => $code,
            'data' => $data?:[],
            'success' => $status == Response::HTTP_OK,
        ], $status);
    }

    public static function respuestaDTOErrorInput($message, array $errors)
    {
        return response([
            'message' => $message,
            'errors' => $errors
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
