<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: PermissionsController.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;

class PermissionsController extends Controller
{
    /**
     * Get all Permissions
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Permission::all());
    }

    /**
     * Get Permission by ID
     *
     * @param  string  $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        return response()->json(Permission::findById($id));
    }

    /**
     * Get all permission of the Authenticate User
     *
     * @return JsonResponse
     */
    public function permissions(): JsonResponse
    {
        $permissions = auth()->user()->roles()->with('permissions')->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->toArray();

        return response()->json($permissions);
    }
}
