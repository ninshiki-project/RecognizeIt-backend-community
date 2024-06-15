<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Enum\PostTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostsPostRequest;
use App\Models\Posts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
     * @return JsonResponse
     */
    public function store(PostsPostRequest $request)
    {
        /**
         *  Manual Validation if the remaining points sufficed to reward
         */
        $pointsToConsume = count($request->recipient_id) * $request->points;
        $user = $request->user();

        if ($request->type === PostTypeEnum::User && $pointsToConsume > $user->points->credits) {
            throw ValidationException::withMessages([
                'points' => 'insufficient credits left',
            ]);
        }
        $post = Posts::create($request->only(['posted_by', 'type', 'content']));
        $recipients = collect($request->recipient_id)->map(function ($item) {
            return [
                'user_id' => $item,
            ];
        });
        $post->recipients()->createMany($recipients);

        return response()->json($post, Response::HTTP_CREATED);

    }

    /**
     * Display Post by ID
     *
     *
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
     * @return JsonResponse
     */
    public function destroy(Posts $posts)
    {
        $posts->delete();

        return response()->json('', Response::HTTP_NO_CONTENT);
    }
}
