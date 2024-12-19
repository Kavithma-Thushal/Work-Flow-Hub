<?php

use App\Enums\HttpStatus;
use App\Http\Middleware\PermissionMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['permissions' => PermissionMiddleware::class]);
        $middleware->redirectGuestsTo(function () {
            throw new HttpException(HttpStatus::UNAUTHORIZED, 'Unauthenticated');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Exception $e) {
            $status = $e instanceof HttpException ? $e->getStatusCode() : 500;
            return response()->json(['error' => [config('common.generic_error') => $e->getMessage()]], $status);
        });
    })->create();
