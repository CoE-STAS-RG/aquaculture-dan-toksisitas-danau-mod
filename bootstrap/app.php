<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;

use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        //
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle errors properly for production
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Server Error',
                    'error' => app()->environment('local') ? $e->getMessage() : 'An error occurred'
                ], 500);
            }
        });
    })->create();

    // ->withRouteMiddleware([
    //     'role' => \App\Http\Middleware\RoleMiddleware::class, // tambahkan ini
    // ])  ->create();
