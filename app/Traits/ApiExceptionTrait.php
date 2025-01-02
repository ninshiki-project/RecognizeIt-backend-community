<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: ApiExceptionTrait.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Traits;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

trait ApiExceptionTrait
{
    public function renderApiException(Throwable $exception): JsonResponse
    {
        $responseData = $this->prepareApiExceptionData($exception);
        $payload = Arr::except($responseData, 'statusCode');
        $statusCode = $responseData['statusCode'];

        return response()->json([
            'error' => $payload,
        ], $statusCode);
    }

    private function prepareApiExceptionData(Throwable $exception): array
    {
        $responseData['success'] = false;
        $message = $exception->getMessage();

        if ($exception instanceof NotFoundHttpException) {
            $responseData['message'] = empty($message) ? 'Resource not found' : $message;
            $responseData['statusCode'] = 404;
            $responseData['code'] = 404;
            $responseData['type'] = 'NotFoundHttpException';
        } elseif ($exception instanceof QueryException) {
            $responseData['message'] = $message;
            $responseData['statusCode'] = 405;
            $responseData['code'] = 405;
            $responseData['type'] = 'QueryException';
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $responseData['message'] = $message;
            $responseData['statusCode'] = 405;
            $responseData['code'] = 405;
            $responseData['type'] = 'MethodNotAllowedHttpException';
        } elseif ($exception instanceof ModelNotFoundException) {
            $responseData['message'] = "Unable to locate the {$this->modelNotFoundMessage($exception)} you requested.";
            $responseData['statusCode'] = 404;
            $responseData['code'] = 404;
            $responseData['type'] = 'ModelNotFoundException';
        } elseif ($exception instanceof AuthenticationException) {
            $responseData['message'] = 'Unauthenticated';
            $responseData['statusCode'] = 401;
            $responseData['code'] = 401;
            $responseData['type'] = 'AuthenticationException';
        } elseif ($exception instanceof ValidationException) {
            $responseData['message'] = $message;
            $responseData['errors'] = $exception->validator->errors();
            $responseData['statusCode'] = 422;
            $responseData['code'] = 422;
            $responseData['type'] = 'ValidationException';
        } elseif ($exception instanceof ThrottleRequestsException) {
            $responseData['message'] = $exception->getMessage();
            $responseData['statusCode'] = 429;
            $responseData['code'] = 429;
            $responseData['type'] = 'ThrottleRequestsException';
        } elseif ($exception instanceof HttpResponseException) {
            $responseData['message'] = $exception->getResponse()->getContent();
            $responseData['statusCode'] = $exception->getResponse()->getStatusCode() ?? null;
            $responseData['code'] = $exception->getResponse()->getStatusCode();
            $responseData['type'] = $exception->getResponse()->getStatusCode() === 429 ? 'ThrottleRequestsException' : 'HttpResponseException';
        } else {
            $responseData['message'] = $this->prepareExceptionMessage($exception);
            $responseData['statusCode'] = ($exception instanceof HttpExceptionInterface) ? $exception->getStatusCode() : 500;
            $responseData['code'] = ($exception instanceof HttpExceptionInterface) ? $exception->getStatusCode() : 500;
            $responseData['type'] = 'serverErrorException';
            if ($debug = $this->extractExceptionData($exception)) {
                $responseData['debug'] = $debug;
            }
        }

        return $responseData;
    }

    private function prepareExceptionMessage(Throwable $exception): ?string
    {
        $exceptionMessage = $exception->getMessage();

        if (Str::contains($exceptionMessage, 'syntax error')) {
            $exceptionMessage = 'Server error';
        }

        return $exceptionMessage;
    }

    private function modelNotFoundMessage(ModelNotFoundException $exception): string
    {
        if (! is_null($exception->getModel())) {
            return Str::lower(ltrim(preg_replace('/[A-Z]/', ' $0', class_basename($exception->getModel()))));
        }

        return 'resource';
    }

    private function extractExceptionData(Throwable $exception): array
    {
        if (config('app.debug') && ! ($exception instanceof HttpExceptionInterface)) {
            $data = [
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => collect($exception->getTrace())->map(function ($trace) {
                    return Arr::except($trace, ['args']);
                })->all(),
            ];
        } else {
            $data = [];
        }

        return $data;
    }
}
