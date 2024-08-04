<?php

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
     * @return JsonResponse
     */
    public function show($id)
    {
        return response()->json(Role::findById($id));
    }
}
