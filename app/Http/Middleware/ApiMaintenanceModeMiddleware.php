<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: ApiMaintenanceModeMiddleware.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use ninshikiProject\GeneralSettings\Models\GeneralSetting;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiMaintenanceModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->is('api/*')) {
            $settings = GeneralSetting::first();
            $moreConfig = $settings->more_configs;
            if (Arr::get($moreConfig, 'maintenance.maintenance_mode')) {
                \Log::warning('Maintenance mode is ON in Admin Panel');
                throw new HttpException(statusCode: 503, message: 'Maintenance mode is enabled on this application.', code: 503);
            }
        }

        return $next($request);
    }
}
