<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
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

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
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
