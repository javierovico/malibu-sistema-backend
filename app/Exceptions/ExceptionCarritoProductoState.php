<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ExceptionCarritoProductoState extends ExceptionSystem
{
    public static function makeEstadoNoAdmitido($att) : self
    {
        return static::createException('Estado `' . $att . '` no esta en la lista de estados admitidos','estadoNoAdmitido','Estado no admitido', Response::HTTP_NOT_ACCEPTABLE);
    }

    public static function makeEstadoNoRetroceso($estadoOriginal, $nuevoEstado) : self
    {
        return static::createException("No se puede retroceder del estado `$estadoOriginal` al estado `$nuevoEstado`",'estadoNoRetroceso','Estado no retroceso', Response::HTTP_NOT_ACCEPTABLE);
    }
}
