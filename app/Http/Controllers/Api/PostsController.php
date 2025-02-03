<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: PostsController.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Enum\PostTypeEnum;
use App\Http\Controllers\Api\Enum\WalletsEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostsPostRequest;
use App\Http\Resources\PostResource;
use App\Jobs\PostRecognizeJob;
use App\Models\Posts;
use App\Models\User;
use App\Services\Mention\MentionParser;
use Bavix\Wallet\Internal\Exceptions\ExceptionInterface;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use MarJose123\NinshikiEvent\Events\Post\NewPostAdded;
use MarJose123\NinshikiEvent\Events\Post\PostMentionUser;
use MarJose123\NinshikiEvent\Events\Post\PostToggleLike;
use Symfony\Component\HttpFoundation\Response;

class PostsController extends Controller
{
    private CloudinaryEngine $uploadedAsset;

    protected static string $cacheKey = 'posts';

    /**
     *  Retrieve All Post
     *
     * @param  Request  $request
     * @return AnonymousResourceCollection<LengthAwarePaginator<PostResource>>
     */
    public function index(Request $request)
    {
        return Cache::flexible(static::$cacheKey.'pp'.$request->per_page.'page'.$request->page, [5, 10], function () use ($request) {
            return PostResource::collection(
                Posts::with(['recipients', 'likers'])
                    ->orderByDesc('created_at')
                    ->paginate(
                        perPage: $request->per_page ?? 15,
                        page: $request->page ?? 1,
                    )
            );
        });

    }

    /**
     * Retrieve Specific Post
     *
     * @param  Request  $request
     * @param  Posts  $post
     * @return AnonymousResourceCollection<PostResource>
     */
    public function show(Request $request, Posts $post)
    {
        return Cache::flexible(static::$cacheKey.$post->id, [5, 10], function () use ($post) {
            return PostResource::make($post);
        });

    }

    /**
     * Fetch all post that contains specific hashtags
     *
     * @param  Request  $request
     * @param  string  $hashtags
     * @return AnonymousResourceCollection<LengthAwarePaginator<PostResource>>
     */
    public function fetchHashtags(Request $request, string $hashtags)
    {
        if (Str::contains($hashtags, '#')) {
            $hashtags = Str::after($hashtags, '#');
        }

        return Cache::flexible(static::$cacheKey.$hashtags, [5, 10], function () use ($hashtags) {
            return PostResource::collection(
                Posts::with(['recipients', 'likers'])
                    ->orderByDesc('created_at')
                    ->where('content', 'like', '%#'.$hashtags.'%')
            );
        });

    }

    /**
     * Delete Post
     *
     * @param  Request  $request
     * @param  Posts  $post
     * @return JsonResponse|ValidationException
     *
     * @throws ExceptionInterface
     */
    public function destroy(Request $request, Posts $post): JsonResponse|ValidationException
    {
        // validation
        if ($post->originalPoster->id !== auth()->user()->id) {
            throw ValidationException::withMessages([
                'email' => ['You are not allowed to delete the post that you are not the author.'],
            ]);
        }

        // refund the wallet of the user for the consumed coins
        $amountToRefund = $post->recipients->count();
        $wallet = $post->originalPoster->getWallet(WalletsEnum::SPEND->value);
        $wallet->deposit($amountToRefund, [
            'title' => 'Ninshiki Spend',
            'description' => 'Refund funds after deleting the post.',
            'post_created_at' => $post->created_at,
            'date_at' => Carbon::now(),
        ]);

        $post->recipients()->each(function ($recipient) use ($post) {
            $wallet = $recipient->getWallet(WalletsEnum::DEFAULT->value);
            $wallet->withdraw($post->points_per_recipient, [
                'title' => 'Ninshiki Wallet',
                'description' => 'Deduction funds after deleting the post.',
                'post_created_at' => $post->created_at,
                'poster' => $post->originalPoster,
                'date_at' => Carbon::now(),
            ]);
        });

        if ($post->attachment_type === 'image' && $post->cloudinary_id) {
            $cloudinary = new CloudinaryEngine;
            $cloudinary->destroy($post->cloudinary_id);
        }

        $post->delete();

        /**
         * @status 200
         */
        return response()->json([
            'success' => true,
            'message' => 'post deleted',
        ], Response::HTTP_OK);

    }

    /**
     * Update Post Content
     *
     * @param  Request  $request
     * @param  Posts  $post
     * @return JsonResponse|ValidationException
     */
    public function update(Request $request, Posts $post): JsonResponse|ValidationException
    {

        // prevent updating the post if his not the author
        if ($post->originalPoster->id !== auth()->user()->id) {
            throw ValidationException::withMessages([
                'email' => ['You are not allowed to update the post that you are not the author.'],
            ]);
        }
        // the user can only update the content before 5 minutes after it was posted.
        if (Carbon::parse($post->created_at)->diffInMinutes(Carbon::now()) > 5) {
            throw ValidationException::withMessages([
                'email' => ['You are not allowed anymore to update the post after 5 minutes it was posted.'],
            ]);
        }

        $request->validate([
            'post_content' => ['sometimes', 'string'],
            'attachment_type' => [Rule::in(['gif', 'image']), 'sometimes'],
            'gif_url' => ['required_if:attachment_type,gif', 'url', 'sometimes'],
            'image' => ['required_if:attachment_type,image', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048', 'sometimes'],
        ]);

        // Can update only content including the attachment
        $oldModel = $post;

        if ($oldModel->cloudinary_id) {
            $cloudinary = new CloudinaryEngine;
            $cloudinary->destroy($post->cloudinary_id);
        }
        if ($request->has('image')) {
            $uud = Str::uuid()->toString();
            $fileName = "{$request->user()->id}-{$uud}";
            $this->uploadedAsset = $request->image->storeOnCloudinaryAs('posts', $fileName);
        }

        $post->update([
            'content' => $request->post_content && Str::of($request->post_content)->length() > 5 ? $request->post_content : $oldModel->content,
            'attachment_type' => $request->attachment_type,
            'cloudinary_id' => $request->has('image') ? $this->uploadedAsset->getSecurePath() : null,
            'attachment_url' => $request->has('image') ? $this->uploadedAsset->getSecurePath() : $request->gif_url,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'post updated',
            'data' => new PostResource($post),
        ], Response::HTTP_OK);

    }

    /**
     * Create New Post
     *
     * @param  PostsPostRequest  $request
     * @return JsonResponse|ValidationException
     *
     * @throws ExceptionInterface
     */
    public function store(PostsPostRequest $request): JsonResponse|ValidationException
    {
        /**
         * Toggle Rate Limit
         */
        $request->rateLimitValidate();

        /**
         *  Manual Validation if the remaining points sufficed to reward
         */
        $totalFundsToDeduct = count($request->recipient_id) * $request->amount;
        $wallet = auth()->user()->getWallet(WalletsEnum::SPEND->value);

        if ($request->type === PostTypeEnum::User->value && $totalFundsToDeduct > $wallet->balanceInt) {
            throw ValidationException::withMessages([
                'amount' => 'Insufficient Available Credits.',
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
        $post = Posts::create([
            'content' => $request->post_content,
            'points_per_recipient' => $request->amount,
            'attachment_type' => $request->attachment_type,
            'cloudinary_id' => $request->has('image') ? $this->uploadedAsset->getSecurePath() : null,
            'attachment_url' => $request->has('image') ? $this->uploadedAsset->getSecurePath() : $request->gif_url,
            'type' => $request->type,
            'posted_by' => $request->user()->id,
        ]);
        /**
         *  Link the User who will receive the points to the post via middle table
         */
        if ($post) {
            /** @var array<string, string> $recipientsIds */
            $recipientsIds = $request->recipient_id ?? [];

            $recipients = collect($recipientsIds)->map(function ($item) {
                return [
                    'user_id' => $item,
                ];
            });
            $recipientsInstance = $post->recipients()->createMany($recipients);
            /**
             *  Distribute the points to each recipient
             */
            $recipients->each(function ($item) use ($request) {
                $_user = User::findOrFail($item['user_id']);
                $defaultWallet = $_user->getWallet(WalletsEnum::DEFAULT->value);
                /** @var int $amount */
                $amount = $request->amount ?? 0;
                $defaultWallet->deposit($amount, [
                    'title' => 'Ninshiki Wallet',
                    'description' => 'Added funds for being recognize by your colleague',
                    'date_at' => Carbon::now(),
                ]);
                // send email notification and application notification
                PostRecognizeJob::dispatch($_user)
                    ->delay(now()->addMinutes(2))
                    ->afterCommit()
                    ->afterResponse();

            });
            /**
             *  Deduct all the points to the user who posted a post
             */
            $wallet->withdraw($totalFundsToDeduct, [
                'title' => 'Spend Wallet',
                'description' => 'Deduction from posting recognition',
                'date_at' => Carbon::now(),
            ]);

            /**
             * Dispatch an event for the new post
             */
            NewPostAdded::dispatch($post, $recipientsInstance);

            /**
             * Increment the Rate Limit
             */
            $request->rateLimitIncrease();

            /**
             * Parser any mention user from the post
             * and trigger an event
             */
            new MentionParser($post);
            PostMentionUser::dispatch($post, $recipientsInstance);

            /**
             * @status 201
             */
            return response()->json([
                'success' => true,
                'message' => 'post created',
                'post' => new PostResource($post),
            ], Response::HTTP_CREATED);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ], Response::HTTP_BAD_REQUEST);
        }

    }

    /**
     * Like/Unlike Post
     *
     * This route will have automatically used the authenticated user to Like/Unlike the Post
     *
     * @param  Posts  $posts  supply the post_id that the user will like/unlike
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function toggleLike(Posts $posts): JsonResponse
    {
        $user = User::find(auth()->user()->id);
        $user->toggleLike($posts);

        /**
         * Dispatch an event for toggle like
         */
        PostToggleLike::dispatch($posts, auth()->user());

        /**
         * @status 200
         */
        return response()->json([
            'success' => true,
        ], Response::HTTP_OK);
    }
}
