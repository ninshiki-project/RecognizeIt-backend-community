<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'laravel_version' => app()->version(),
        'php_version' => PHP_VERSION,
    ]);
});
