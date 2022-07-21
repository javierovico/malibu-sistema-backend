<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ExceptionCarritoProductoState extends ExceptionSystem
{
    public static function makeEstadoNoAdmitido($att) : self
    {
        return self::createException('Estado `' . $att . '` no esta en la lista de estados admitidos','estadoNoAdmitido','Estado no admitido', Response::HTTP_NOT_ACCEPTABLE);
    }
}
