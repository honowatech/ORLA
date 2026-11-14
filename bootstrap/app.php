<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend([
            \App\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        $middleware->append([
            \App\Http\Middleware\Filter::class,
            \App\Http\Middleware\Coursier_filter::class,
            \App\Http\Middleware\Admin_filter::class,
        ]);

        $middleware->alias([
            'filter' => \App\Http\Middleware\Filter::class,
            'coursier_filter' => \App\Http\Middleware\Coursier_filter::class,
            'admin_filter' => \App\Http\Middleware\Admin_filter::class,
            'Check_Sa_Client_Error' => \App\Http\Middleware\Check_Sa_Client_Error::class,
            'api.active' => \App\Http\Middleware\Api\EnsureAccountIsActive::class,
            'api.role' => \App\Http\Middleware\Api\EnsureUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
        ]);

        // L'API répond toujours en JSON, même si l'en-tête Accept est absent.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson()
        );

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Ressource introuvable.'], 404);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Non authentifié.', 'code' => 'non_authentifie'], 401);
            }
        });
    })
    ->create();
