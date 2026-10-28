<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsHr;
use App\Http\Middleware\EnsureUserIsIntern;
use App\Http\Middleware\EnsureUserIsSupervisor;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(
            guests: '/login',
            users: function (Request $request) {
                $user = $request->user();

                return match ($user?->role) {
                    'hr' => route('hr.dashboard'),
                    'supervisor' => route('supervisor.dashboard'),
                    'admin' => route('admin.dashboard'),
                    default => route('dashboard.intern'),
                };
            }
        );

        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'hr' => EnsureUserIsHr::class,
            'intern' => EnsureUserIsIntern::class,
            'supervisor' => EnsureUserIsSupervisor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
