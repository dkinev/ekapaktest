<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {

            if (!$request->expectsJson()) {
                return null;
            }

            // Validation
            if ($e instanceof ValidationException) {
                return response()->json([
                    'error'   => 'validation_error',
                    'message' => 'Validation failed',
                    'details' => $e->errors()
                ], 422);
            }

            // HTTP exceptions (404, 403, 429 и т.д.)
            if ($e instanceof HttpExceptionInterface) {
                return response()->json([
                    'error'   => 'http_error',
                    'message' => $e->getMessage() ?: 'HTTP error'
                ], $e->getStatusCode());
            }

            // Domain exceptions (ВАЖНО для твоей state machine)
            if ($e instanceof \DomainException) {
                return response()->json([
                    'error'   => 'domain_error',
                    'message' => $e->getMessage()
                ], 400);
            }

            // Fallback
            return response()->json([
                'error'   => 'server_error',
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Internal Server Error'
            ], 500);
        });
    })->create();
