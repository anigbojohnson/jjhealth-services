<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(
            \App\Http\Middleware\OpenTelemetryMiddleware::class
        );

        $middleware->alias([
            'idempotency' => \App\Http\Middleware\IdempotencyMiddleware::class,
        ]);
    })
->withExceptions(function (Exceptions $exceptions) {

    $exceptions->render(function (
        \App\Exceptions\IdempotencyKeyMismatchException $e,
        \Illuminate\Http\Request $request
    ) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => [
                    'code' => 'IDEMPOTENCY_KEY_REUSED',
                ],
            ], 409);
        }
    });

})->withExceptions(function (Exceptions $exceptions) {

    $exceptions->render(function (
        \App\Exceptions\IdempotencyKeyMismatchException $e,
        \Illuminate\Http\Request $request
    ) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => [
                    'code' => 'IDEMPOTENCY_KEY_REUSED',
                ],
            ], 409);
        }
    });

})

    ->create();
