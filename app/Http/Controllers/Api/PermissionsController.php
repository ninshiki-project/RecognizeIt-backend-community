<?php

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
     * @return JsonResponse
     */
    public function show($id)
    {
        return response()->json(Permission::findById($id));
    }

    /**
     * Get all permission of the Authenticate User
     *
     * @return JsonResponse
     */
    public function permissions()
    {
        $permissions = auth()->user()->roles()->with('permissions')->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->toArray();

        return response()->json($permissions);
    }
}
