<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostsPostRequest;
use App\Models\Posts;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use LaravelIdea\Helper\App\Models\_IH_Posts_C;
use Symfony\Component\HttpFoundation\Response;

class PostsController extends Controller
{
    /**
     *  Get All Posts
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Posts::all());
    }

    /**
     * Create New Post
     *
     * @param PostsPostRequest $request
     * @return JsonResponse
     */
    public function store(PostsPostRequest $request)
    {
        return response()->json(Posts::create($request->validated()), Response::HTTP_CREATED);

    }

    /**
     * Display Post by ID
     *
     *
     * @param Posts $posts
     * @return JsonResponse
     */
    public function show(Posts $posts)
    {
        return response()->json($posts);
    }

    /**
     * Update Post
     *
     *
     * @param Request $request
     * @param Posts $posts
     * @return JsonResponse
     */
    public function update(Request $request, Posts $posts)
    {
        $posts->update($request->all());

        return response()->json($posts);
    }

    /**
     * Delete Post
     *
     *
     * @param Posts $posts
     * @return JsonResponse
     */
    public function destroy(Posts $posts)
    {
        $posts->delete();

        return response()->json('', Response::HTTP_NO_CONTENT);
    }
}
