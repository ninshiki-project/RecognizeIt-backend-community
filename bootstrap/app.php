<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/status',
    )
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('auth:clear-resets')->everyFifteenMinutes();
    })
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(fn (Request $request) => $request->expectsJson() || $request->ajax());
        $exceptions->stopIgnoring(HttpException::class);
        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => [
                        'message' => 'Record Not Found',
                        'code' => $exception->getStatusCode(),
                    ],
                ], Response::HTTP_NOT_FOUND);
            }
            return $exception;
        });
        $exceptions->render(function (HttpException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => [
                        'message' => $exception->getMessage(),
                        'code' => $exception->getStatusCode(),
                    ],
                ], $exception->getStatusCode());
            }
            return $exception;
        });
        //
    })->create();
