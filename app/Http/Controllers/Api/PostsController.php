<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Enum\PostTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostsPostRequest;
use App\Http\Resources\PostResource;
use App\Models\Posts;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class PostsController extends Controller
{
    private CloudinaryEngine $uploadedAsset;

    private Posts $post;

    /**
     *  Get All Posts
     *
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return PostResource::collection(
            Posts::with(['recipients', 'likes'])
                ->orderByDesc('created_at')
                ->fastPaginate(
                    perPage: $request->perPage ?? 15,
                    page: $request->page ?? 1,
                )
        );
    }

    /**
     * Create New Post
     *
     * @param  PostsPostRequest  $request
     * @return JsonResponse
     */
    public function store(PostsPostRequest $request): JsonResponse
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
            $fileName = "{$request->user()->id}-{$uud}";
            $this->uploadedAsset = $request->image->storeOnCloudinaryAs('posts', $fileName);
        }
        $this->post = Posts::create([
            'content' => $request->post_content,
            'attachment_type' => $request->attachment_type,
            'attachment_url' => $request->has('image') ? $this->uploadedAsset->getSecurePath() : $request->gif_url,
            'type' => $request->type,
            'posted_by' => $request->user()->id,
        ]);
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
        $recipients->each(function ($item) use ($request) {
            User::findOrFail($item['user_id'])->points->increment('points_earned', $request->points);
        });
        /**
         *  Deduct all the points to the user who posted a post
         */
        $user->points->decrement('credits', $pointsToConsume);

        return response()->json([
            'success' => true,
            'message' => 'post created',
            'post' => $this->post,
        ], Response::HTTP_CREATED);

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
     * @param Posts $posts
     * @return JsonResponse
     */
    public function destroy(Posts $posts): JsonResponse
    {
        $posts->delete();

        return response()->json('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param  Posts  $posts
     * @return JsonResponse
     */
    public function like(Posts $posts): JsonResponse
    {
        $existing = $posts->likes()->where('user_id', auth()->id())->first();
        if ($existing) {
            $existing->delete();
        } else {
            $posts->likes()->create([
                'user_id' => auth()->id(),
            ]);
        }

        return response()->json([
            'success' => true,
        ], Response::HTTP_ACCEPTED);
    }
}
