<?php

namespace App\Exceptions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ExceptionSystem extends \Exception
{
    public string $codigo = 'CodigoDesconocido';
    public string $titulo = 'Titulo';
    public int $statusHTTP = Response::HTTP_INTERNAL_SERVER_ERROR;
    public ?array $errors = null;

    public static function createException($mensaje, $codigoString, $titulo, $statusHttp = Response::HTTP_INTERNAL_SERVER_ERROR) : self
    {
        $exception = new self($mensaje);
        $exception->codigo = $codigoString;
        $exception->titulo = $titulo;
        $exception->statusHTTP = $statusHttp;
        return $exception;
    }

    public static function createFromOther(\Throwable $e): self
    {
        return self::createException($e->getMessage(),$e->getFile(),substr($e->getMessage(),0,300));
    }

    public static function createFromValidationException(ValidationException $e): self
    {
        $exeption = self::createException($e->getMessage(), 'error', 'Error generado con los datos de entrada', Response::HTTP_UNPROCESSABLE_ENTITY);
        $exeption->errors = $e->errors();
        return $exeption;
    }

    public static function createExceptionInput($keyInput, $errors): self
    {
        $exeption = self::createException("The given data was invalid.", 'error', 'Error con los siguientes datos', Response::HTTP_UNPROCESSABLE_ENTITY);
        $exeption->errors = [
            $keyInput => $errors
        ];
        return $exeption;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function render(Request $request): \Illuminate\Http\Response
    {
        return Controller::respuestaDTO(
            $this->titulo,
            $this->getMessage(),
            $this->codigo,
            [
                'class' => get_class($this),
                'errors' => $this->errors?:[],
            ],
            $this->statusHTTP
        );
    }
}
