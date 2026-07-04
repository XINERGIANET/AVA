<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
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
        //
    }

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'status' => false,
                    'code' => 'SESSION_EXPIRED',
                    'message' => 'Tu sesión ha expirado. Inicia sesión nuevamente para continuar.',
                    'login_url' => route('login'),
                ], 401);
            }

            if ($exception instanceof TokenMismatchException) {
                return response()->json([
                    'status' => false,
                    'code' => 'CSRF_EXPIRED',
                    'message' => 'La sesión de seguridad expiró. Inicia sesión nuevamente antes de reintentar.',
                    'login_url' => route('login'),
                ], 419);
            }

            if ($exception instanceof HttpExceptionInterface) {
                $messages = [
                    403 => 'No tienes permiso para realizar esta operación.',
                    404 => 'El recurso solicitado ya no existe o fue movido.',
                    405 => 'Esta operación no está disponible.',
                    429 => 'Se realizaron demasiados intentos. Espera un momento y vuelve a intentar.',
                ];
                $status = $exception->getStatusCode();
                if (isset($messages[$status])) {
                    return response()->json(['status' => false, 'message' => $messages[$status]], $status);
                }
            }

            if (!$exception instanceof ValidationException) {
                $errorId = 'SYS-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(), -6));
                $this->report($exception);

                return response()->json([
                    'status' => false,
                    'message' => "Ocurrió un error inesperado. Intenta nuevamente. Si continúa, informa el código {$errorId}.",
                    'error_id' => $errorId,
                ], 500);
            }
        }

        return parent::render($request, $exception);
    }
}
