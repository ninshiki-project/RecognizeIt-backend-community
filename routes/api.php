<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: api.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\DepartmentsController;
use App\Http\Controllers\Api\DesignationsController;
use App\Http\Controllers\Api\PermissionsController;
use App\Http\Controllers\Api\PostsController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RedeemController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Middleware\ApiMaintenanceModeMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([
    ApiMaintenanceModeMiddleware::class,
])->group(function () {

    /**
     *  Authentication Route
     */
    Route::post('/login/credentials', [AuthenticationController::class, 'loginViaEmail'])
        ->middleware('throttle:login');
    Route::get('/login/{provider}', [AuthenticationController::class, 'loginViaProvider']);
    Route::post('/login/{provider}', [AuthenticationController::class, 'providerCallback']);
    Route::post('/logout', [AuthenticationController::class, 'logout'])->middleware('auth:sanctum');

    /**
     *  Main Route
     */
    Route::prefix('/v1')->middleware('auth:sanctum')->group(function () {
        // Department
        Route::apiResource('departments', DepartmentsController::class);

        // Users
        Route::get('users/{username}', [UserController::class, 'showByUsername']);
        Route::apiResource('users', UserController::class)->except(['store', 'update']);

        // Profile - Authenticated User
        Route::group(['prefix' => '/auth'], function () {
            Route::get('me', [ProfileController::class, 'me']);
            Route::patch('/change-password', [ProfileController::class, 'changePassword']);
            Route::post('/forgot-password', [ProfileController::class, 'forgotPassword'])
                ->middleware(['throttle:forgotPassword'])
                ->withoutMiddleware('auth:sanctum');
            Route::post('/reset-password', [ProfileController::class, 'resetPassword'])
                ->withoutMiddleware('auth:sanctum');
        });

        Route::prefix('/sessions')->group(function () {
            // Browser Session
            Route::get('/', [SessionController::class, 'loginSessions']);
            // logout other Device session
            Route::post('/logout/devices', [SessionController::class, 'logoutOtherDevices']);
            // session health
            Route::get('/health', [SessionController::class, 'health'])->name('session.health');
        });

        // Permissions
        Route::get('profile/permissions/', [PermissionsController::class, 'permissions']);
        Route::apiResource('permissions', PermissionsController::class)->only(['index', 'show']);

        // Role
        Route::apiResource('roles', RolesController::class)->only(['index', 'show']);

        // Post
        Route::patch('posts/{posts}/toggle/like', [PostsController::class, 'toggleLike']);
        Route::get('posts/hashtags/{hashtags}', [PostsController::class, 'fetchHashtags']);
        Route::apiResource('posts', PostsController::class);

        // Designation / Position / Job Title
        Route::get('designation', [DesignationsController::class, 'index']);

        // Products
        Route::apiResource('products', ProductController::class);

        // Shop
        Route::apiResource('shop', ShopController::class)->only(['index', 'store', 'destroy']);

        // Redeem
        Route::prefix('redeems')->group(function () {
            Route::get('/', [RedeemController::class, 'index']);
            Route::post('/shop', [RedeemController::class, 'store']);
            Route::delete('/{id}', [RedeemController::class, 'cancel']);
            Route::patch('/{id}', [RedeemController::class, 'status']);
            Route::get('/{id}', [RedeemController::class, 'show']);
        });

        // Wallets
        Route::prefix('wallets')->group(function () {
            Route::get('/default/balance', [WalletController::class, 'defaultWalletBalance']);
            Route::get('/spend/balance', [WalletController::class, 'spendWalletBalance']);
            Route::get('/currency/balance', [WalletController::class, 'currencyWalletBalance']);
        });

        // Application
        Route::prefix('application')->group(function () {
            Route::get('/', [ApplicationController::class, 'index']);
        });

    });
});
