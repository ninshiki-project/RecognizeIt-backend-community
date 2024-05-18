<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get all users
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(User::all());
    }

    /**
     * Invite User
     *
     * @return void
     */
    public function store(Request $request)
    {
    }

    public function show($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }

    /**
     * Get all Points of User
     *
     * @return JsonResponse
     */
    public function showPoints(User $user)
    {
        return response()->json($user->points);
    }
}
