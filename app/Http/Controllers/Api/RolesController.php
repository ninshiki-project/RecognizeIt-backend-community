<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
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

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
