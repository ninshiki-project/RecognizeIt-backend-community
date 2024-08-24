<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PointsController extends Controller
{
    /**
     * Get Points of Authenticated User
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(
            auth()->user()->points
        );
    }
}
