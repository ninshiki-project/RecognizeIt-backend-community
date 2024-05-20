<?php

use App\Http\Controllers\Api\DepartmentsController;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\PointsController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login/credentials', [LoginController::class, 'loginViaEmail']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/login/{provider}', [LoginController::class, 'loginViaProvider']);
Route::post('/login/{provider}', [LoginController::class, 'providerCallback']);

Route::prefix('/v1')->middleware('auth:sanctum')->group(function () {
    // Department
    Route::apiResource('departments', DepartmentsController::class);
    // Users
    Route::group(['prefix' => '/users'], function () {
        Route::get('/{user}/points', [UserController::class, 'showPoints']);
        Route::post('/invite', [UserController::class, 'inviteUser']);
    });
    Route::apiResource('users', UserController::class)->except(['store', 'update']);
    // Points
    Route::get('points', [PointsController::class, 'index']);
    // Invitation
    Route::group(['prefix' => '/invitations'], function () {
        Route::get('/', [InvitationController::class, 'index']);
        Route::patch('/', [InvitationController::class, 'invitation']);
    });
    // Profile - Authenticated User
    Route::get('me', [ProfileController::class, 'me']);

});
