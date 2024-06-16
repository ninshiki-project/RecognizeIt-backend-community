<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Enum\PostTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostsPostRequest;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class PostsController extends Controller
{
    private string $uploadedAsset;

    private Posts $post;

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
            $uud = Str::uuid()->toString();
            $fileName = "{$request->user()->id}-{$uud}.{$request->file('image')->getClientOriginalExtension()}";
            $this->uploadedAsset = $request->file('image')->storeOnCloudinaryAs('posts', $fileName);
            $this->post = Posts::create([
                'content' => $request->content,
                'image' => $this->uploadedAsset,
                'type' => $request->type,
                'posted_by' => $request->user()->id,
            ]);
        } else {
            $this->post = Posts::create($request->only(['posted_by', 'type', 'content']));
        }
        /**
         *  Link the User who will receive the points to the post via middle table
         */
        $recipients = collect($request->recipient_id)->map(function ($item) {
            return [
                'user_id' => $item,
            ];
        });
        $this->post->recipients()->createMany($recipients);
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
