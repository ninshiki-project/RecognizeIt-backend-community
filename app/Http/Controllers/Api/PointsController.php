<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Points;
use Illuminate\Http\JsonResponse;

class PointsController extends Controller
{
    /**
     * Get all Points
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Points::with('user')->get()->toArray());
    }
}
