<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concern\CanPurgeCache;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentPostRequest;
use App\Http\Requests\DepartmentPutRequest;
use App\Models\Departments;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class DepartmentsController extends Controller
{
    use CanPurgeCache;

    protected static string $cacheKey = 'departments';

    /**
     * Get all Departments
     *
     * @return JsonResponse
     */
    public function index()
    {
        return Cache::remember(self::$cacheKey, now()->addDays(5), function () {
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
        /**
         * Removed Cache
         */
        $this->purgeCache();

        return response()->json(Departments::create($request->all()));
    }

    /**
     * Display Department
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        return Cache::remember(self::$cacheKey.$id, now()->addDays(5), function () use ($id) {
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
        /**
         * Removed Cache
         */
        $this->purgeCache();
        $this->purgeCache(static::$cacheKey.$id);

        return response()->json(Departments::findOrFail($id)->update($request->all()));
    }

    /**
     * Delete Department
     *
     * @return null JsonResponse There is no response
     */
    public function destroy($id)
    {
        /**
         * Removed Cache
         */
        $this->purgeCache();
        $this->purgeCache(static::$cacheKey.$id);

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
