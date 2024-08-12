<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\DepartmentsController;
use App\Http\Controllers\Api\DesignationsController;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\PermissionsController;
use App\Http\Controllers\Api\PointsController;
use App\Http\Controllers\Api\PostsController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login/credentials', [AuthenticationController::class, 'loginViaEmail'])
    ->middleware('throttle:login');
Route::post('/logout', [AuthenticationController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/login/{provider}', [AuthenticationController::class, 'loginViaProvider']);
Route::post('/login/{provider}', [AuthenticationController::class, 'providerCallback']);

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
    Route::group(['prefix' => '/auth'], function () {
        Route::get('me', [ProfileController::class, 'me']);
        Route::patch('/change-password', [ProfileController::class, 'changePassword']);
        Route::post('/forgot-password', [ProfileController::class, 'forgotPassword'])
            ->middleware(['throttle:forgotPassword'])
            ->withoutMiddleware('auth:sanctum');
        Route::post('/reset-password', [ProfileController::class, 'resetPassword'])
            ->withoutMiddleware('auth:sanctum');
        // Browser Session
        Route::get('/sessions', [ProfileController::class, 'getSessionsProperty']);
        // logout other Device session
        Route::post('/logout/devices', [AuthenticationController::class, 'logoutOtherDevices']);
    });

    // Permissions
    Route::get('profile/permissions/', [PermissionsController::class, 'permissions']);
    Route::apiResource('permissions', PermissionsController::class)->only(['index', 'show']);

    // Role
    Route::apiResource('roles', RolesController::class)->only(['index', 'show']);

    // Post
    Route::patch('posts/{posts}/like', [PostsController::class, 'like']);
    Route::apiResource('posts', PostsController::class)->except(['show', 'update', 'destroy']);

    // Designation / Position / Job Title
    Route::get('designation', [DesignationsController::class, 'index']);

});
