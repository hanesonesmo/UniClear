<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

/**
 * bootstrap/app.php — Laravel 13
 *
 * In Laravel 13, middleware aliases are registered here.
 * The old Kernel.php approach no longer applies.
 * 'guest' and 'auth' are built into Laravel 13 — do NOT re-register them.
 * Only register our custom 'role' middleware alias here.
 */
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register our custom role middleware alias
        // Enables ->middleware('role:admin') syntax in routes/web.php
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();