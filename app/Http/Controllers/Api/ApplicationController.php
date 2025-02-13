<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
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
}
