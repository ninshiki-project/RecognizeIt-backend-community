<?php

use Illuminate\Support\Facades\Route;

if (\Composer\InstalledVersions::isInstalled('ninshiki-project/ninshiki')) {
    Route::get('/api', function () {
        return response()->json([
            'laravel' => app()->version(),
            'documentation' => '/docs/api',
            'documentation_version' => config('scramble.info.version'),
            'php_version' => PHP_VERSION,
        ]);
    });

} else {
    Route::get('/', function () {
        return response()->json([
            'laravel' => app()->version(),
            'documentation' => '/docs/api',
            'documentation_version' => config('scramble.info.version'),
            'php_version' => PHP_VERSION,
        ]);
    });
}
