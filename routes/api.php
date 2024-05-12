<?php

use App\Http\Controllers\Api\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/login/{provider}', [LoginController::class, 'loginViaProvider']);
Route::post('/login/{provider}', [LoginController::class, 'providerCallback']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
