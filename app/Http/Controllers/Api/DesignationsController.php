<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DesignationsController extends Controller
{

    /**
     * Get all Job Tittle
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => config('ninshiki.designation'),
        ]);
    }
}
