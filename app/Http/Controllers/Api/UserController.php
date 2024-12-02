<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: UserController.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use MarJose123\NinshikiEvent\Events\User\UserDeleted;

class UserController extends Controller
{
    protected static ?string $cacheKey = 'users';

    /**
     * Get all users
     *
     * @return LengthAwarePaginator<User>
     */
    public function index()
    {
        return Cache::flexible(static::$cacheKey, [5, 10], function () {
            return User::paginate();
        });
    }

    /**
     * Get User by ID
     *
     *
     * @param  string  $id
     * @return JsonResponse
     */
    public function show(string $id)
    {
        return Cache::flexible(static::$cacheKey.$id, [5, 10], function () use ($id) {
            return response()->json(User::findOrFail($id));
        });

    }

    /**
     * Delete User
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {

        $user = User::findOrFail($id);

        /**
         * Dispatch event
         */
        UserDeleted::dispatch($user);

        /**
         * Delete user
         */
        $user->delete();

        return response()->noContent();
    }
}
