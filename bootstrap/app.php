<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/status',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['api', 'auth:sanctum']],
    )
    ->withSchedule(function (Schedule $schedule) {})
    ->withMiddleware(function (Middleware $middleware) {
        $environment = env('APP_ENV');

        switch ($environment) {
            case 'production':
                $middleware->trustProxies(
                    at: '*',
                    headers: Request::HEADER_X_FORWARDED_FOR
                );
                break;

            default:
                // Local/Development Configuration
                $middleware->trustProxies(
                    at: ['127.0.0.1', '::1'],
                    headers: Request::HEADER_X_FORWARDED_FOR |
                    Request::HEADER_X_FORWARDED_PROTO
                );
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->expectsJson() || $request->ajax());
        $exceptions->render(function (Throwable $exception, Request $request) {
            if ($request->is('api/*') && $request->wantsJson()) {
                return app(\App\Exceptions\ApiException::class)->renderApiException($exception);
            }
        });
        //
    })->create();
