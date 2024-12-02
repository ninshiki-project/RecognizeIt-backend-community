<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: DesignationsController.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

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
