<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concern\CanPurgeCache;
use App\Http\Controllers\Api\Enum\PostTypeEnum;
use App\Http\Controllers\Api\Enum\WalletsEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostsPostRequest;
use App\Http\Resources\PostResource;
use App\Jobs\PostRecognizeJob;
use App\Models\Posts;
use App\Models\User;
use Bavix\Wallet\Internal\Exceptions\ExceptionInterface;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class PostsController extends Controller
{
    use CanPurgeCache;

    private CloudinaryEngine $uploadedAsset;

    private Posts $post;

    protected static string $cacheKey = 'posts';

    /**
     *  Get All Posts
     *
     * @param  Request  $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return Cache::remember(static::$cacheKey, Carbon::now()->addDays(2), function () {
            return PostResource::collection(
                Posts::with(['recipients', 'likes'])
                    ->orderByDesc('created_at')
                    ->fastPaginate(
                        perPage: $request->perPage ?? 15,
                        page: $request->page ?? 1,
                    )
            );
        });

    }

    /**
     * Create New Post
     *
     * @param  PostsPostRequest  $request
     * @return JsonResponse
     *
     * @throws ExceptionInterface
     */
    public function store(PostsPostRequest $request): JsonResponse
    {

        /**
         *  Manual Validation if the remaining points sufficed to reward
         */
        $totalFundsToDeduct = count($request->recipient_id) * $request->amount;
        $authenticated_user = $request->user();
        $wallet = $authenticated_user->getWallet(WalletsEnum::SPEND->value);

        if ($request->type === PostTypeEnum::User && $totalFundsToDeduct > $wallet->balanceInt) {
            throw ValidationException::withMessages([
                'points' => 'insufficient fund left',
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
            $_user = User::findOrFail($item['user_id'])->first();
            $defaultWallet = $_user->getWallet(WalletsEnum::DEFAULT->value);
            $defaultWallet->deposit($request->amount, [
                'title' => 'Ninshiki Wallet',
                'description' => 'Added funds for being recognize by your colleague',
                'date_at' => Carbon::now(),
            ]);
            // send email notification and application notification
            PostRecognizeJob::dispatchAfterResponse($_user);

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
         * Removed Cache
         */
        $this->purgeCache();

        /**
         * @status 201
         */
        return response()->json([
            'success' => true,
            'message' => 'post created',
            'post' => $this->post,
        ], Response::HTTP_CREATED);

    }

    /**
     * Like A Post
     *
     * This route will have automatically used the authenticated user to Like/Unlike the Post
     *
     * @param  Posts  $posts  supply the Post ID that the user will be like
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

        /**
         * Removed Cache
         */
        $this->purgeCache();

        /**
         * @status 200
         */
        return response()->json([
            'success' => true,
        ], Response::HTTP_OK);
    }
}
