<?php

namespace App\Http\Controllers\Api;

use App\Http\Concern\HasSanity;
use App\Http\Controllers\Api\Enum\PostTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostsPostRequest;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class PostsController extends Controller
{
    use HasSanity;

    private string $sanityAssit =''

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

        if ($request->type === PostTypeEnum::User && $pointsToConsume > $user->points->credits || $user->points->credits <= 0) {
            throw ValidationException::withMessages([
                'points' => 'insufficient credits left',
            ]);
        }
        /**
         *  Create the Post
         */
        if ($request->has('image')) {
            $post = Posts::create($request->only(['posted_by', 'type', 'content']));
        }
        /**
         *  Link the User who will receive the points to the post via middle table
         */
        $recipients = collect($request->recipient_id)->map(function ($item) {
            return [
                'user_id' => $item,
            ];
        });
        $post->recipients()->createMany($recipients);
        /**
         *  Distribute the points to each recipient
         */
        $recipients->each(function ($item) {
            User::findOrFail($item['user_id'])->points->points_earned++;
        });
        /**
         *  Deduct all the points to the user who posted a post
         */
        $user->points->decrement('credits', $pointsToConsume);

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
