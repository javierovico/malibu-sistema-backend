<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
//        $this->renderable(function (ValidationException $e, $request) {
//            return response([
//                'e' => get_class($e),
//                'validation' => true
//            ]);
//            return response()->view('errors.invalid-order', [], 500);
//        });
        $this->renderable(function (ExceptionSystem $e, $request) {
            return $e->render($request);
        });
        $this->renderable(function (Throwable $e, $request) {
            return ExceptionSystem::createFromOther($e)->render($request);
        });
    }
}
