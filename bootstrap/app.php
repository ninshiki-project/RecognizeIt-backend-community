<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

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
        $exceptions->render(function (QueryException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => [
                        'code' => 500,
                        'message' => $exception->errorInfo[2],
                        'type' => 'QueryException',
                    ],
                ], 500);
            }

            return $exception;
        });
        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => [
                        'code' => $exception->getStatusCode(),
                        'message' => 'Record Not Found',
                        'type' => 'NotFoundHttpException',
                    ],
                ], Response::HTTP_NOT_FOUND);
            }

            return $exception;
        });
        $exceptions->render(function (UnprocessableEntityHttpException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => [
                        'code' => $exception->getStatusCode(),
                        'message' => $exception->getMessage(),
                        'type' => 'UnprocessableEntityHttpException',
                    ],
                ], $exception->getStatusCode());
            }

            return $exception;
        });
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => [
                        'code' => $exception->status,
                        'message' => $exception->getMessage(),
                        'errors' => $exception->errors(),
                        'type' => 'ValidationException',
                    ],
                ], $exception->status);
            }

            return $exception;
        });
        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => [
                        'code' => 401,
                        'message' => $exception->getMessage(),
                        'type' => 'AuthenticationException',
                    ],
                ], 401);
            }

            return $exception;
        });
        $exceptions->render(function (\Symfony\Component\ErrorHandler\Error\FatalError $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => [
                        'code' => 500,
                        'message' => $exception->getMessage(),
                        'error' => $exception->getError(),
                        'type' => 'FatalError',
                    ],
                ], 500);
            }

            return $exception;
        });
        $exceptions->render(function (HttpException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => [
                        'code' => $exception->getStatusCode(),
                        'message' => $exception->getMessage(),
                        'type' => 'HttpException',
                    ],
                ], $exception->getStatusCode());
            }

            return $exception;
        });
        //
    })->create();
