<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentPostRequest;
use App\Http\Requests\DepartmentPutRequest;
use App\Models\Departments;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DepartmentsController extends Controller
{
    /**
     * Get all Departments
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Departments::all());
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
        return response()->json(Departments::findOrFail($id));
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
            $department->users()->each(function ($user) {
                $user->department = null;
                $user->save();
            });
        })->delete();

        return response()->json('', Response::HTTP_ACCEPTED);
    }
}
