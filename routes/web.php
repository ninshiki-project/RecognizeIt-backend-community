<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'laravel_version' => app()->version(),
        'documentation' => '/docs/api',
        'document_version' => config('scramble.info.version'),
        'php_version' => PHP_VERSION,
    ]);
});
