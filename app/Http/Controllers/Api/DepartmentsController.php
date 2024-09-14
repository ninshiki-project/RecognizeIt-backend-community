<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: DepartmentsController.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentPostRequest;
use App\Http\Requests\DepartmentPutRequest;
use App\Models\Departments;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class DepartmentsController extends Controller
{
    protected static string $cacheKey = 'departments';

    /**
     * Get all Departments
     *
     * @return JsonResponse
     */
    public function index()
    {
        return Cache::flexible(self::$cacheKey, [5, 10], function () {
            return response()->json(Departments::all());
        });

    }

    /**
     * Create Department
     *
     * @return JsonResponse
     */
    public function store(DepartmentPostRequest $request)
    {

        return response()->json(Departments::create($request->all()));
    }

    /**
     * Display Department
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        return Cache::flexible(self::$cacheKey.$id, [5, 10], function () use ($id) {
            return response()->json(Departments::findOrFail($id));
        });

    }

    /**
     * Update Department
     *
     * @return JsonResponse
     */
    public function update(DepartmentPutRequest $request, $id)
    {

        return response()->json(Departments::findOrFail($id)->update($request->all()));
    }

    /**
     * Delete Department
     *
     * @return null JsonResponse There is no response
     */
    public function destroy($id)
    {

        Departments::findOrFail($id)->each(function ($department) {
            $department->users()->each(function (User $user) {
                $user->department = null;
                $user->save();
            });
        })->delete();

        // @phpstan-ignore-next-line
        return response()->noContent();
    }
}
