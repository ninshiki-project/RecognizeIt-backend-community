<?php

use App\Http\Controllers\Api\DepartmentsController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\PointsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserInvitationController;
use Illuminate\Support\Facades\Route;

Route::post('/login/credentials', [LoginController::class, 'loginViaEmail']);
Route::get('/login/{provider}', [LoginController::class, 'loginViaProvider']);
Route::post('/login/{provider}', [LoginController::class, 'providerCallback']);

Route::prefix('/v1')->middleware('auth:sanctum')->group(function () {
    // Department
    Route::apiResource('departments', DepartmentsController::class);
    // Users
    Route::get('users/{user}/points', [UserController::class, 'showPoints']);
    Route::post('users/invite', [UserController::class, 'inviteUser']);
    Route::apiResource('users', UserController::class)->except(['store', 'update']);
    // Points
    Route::get('points', [PointsController::class, 'index']);
    // Invitation
    Route::patch('invitation', [UserInvitationController::class, 'invitation']);

});
