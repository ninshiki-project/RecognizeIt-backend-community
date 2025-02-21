<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class ApplicationController extends Controller
{
    protected static string $cacheKey = 'applications';

    /**
     * Retrieve Application Settings
     *
     * @return AnonymousResourceCollection<ApplicationResource>
     */
    public function index()
    {
        return Cache::flexible(static::$cacheKey, [5, 10], function () {
            return ApplicationResource::make(Application::first());
        });
    }

    /**
     * Application Maintenance
     *
     * Check if API is under maintenance
     *
     * @unauthenticated
     *
     * @return JsonResponse
     */
    public function isMaintenance()
    {
        return response()->json([
            'maintenance_mode' => Application::first()->more_configs['maintenance']['maintenance_mode'] ?? false,
        ]);
    }
}
