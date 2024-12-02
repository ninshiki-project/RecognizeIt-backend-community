<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: RolesController.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RolesController extends Controller
{
    /**
     * Get all Roles
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Role::all());
    }

    /**
     * Get Role by ID
     *
     *
     * @param  string  $id
     * @return JsonResponse
     */
    public function show(string $id)
    {
        return response()->json(Role::findById($id, 'web'));
    }
}
