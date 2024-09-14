<?php

namespace App\Http\Controllers\Api;

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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use MarJose123\NinshikiEvent\Events\Post\NewPostAdded;
use MarJose123\NinshikiEvent\Events\Post\PostToggleLike;
use Symfony\Component\HttpFoundation\Response;

class PostsController extends Controller
{
    private CloudinaryEngine $uploadedAsset;

    protected static string $cacheKey = 'posts';

    /**
     *  Get All Posts
     *
     * @param  Request  $request
     * @return AnonymousResourceCollection<LengthAwarePaginator<PostResource>>
     */
    public function index(Request $request)
    {
        return Cache::flexible(static::$cacheKey.'pp'.$request->perPage.'page'.$request->page, [5, 10], function () use ($request) {
            return PostResource::collection(
                Posts::with(['recipients', 'likers'])
                    ->orderByDesc('created_at')
                    ->paginate(
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
        $recipientsInstance = $post->recipients()->createMany($recipients);
        /**
         *  Distribute the points to each recipient
         */
        $recipients->each(function ($item) use ($request) {
            $_user = User::findOrFail($item['user_id']);
            $defaultWallet = $_user->getWallet(WalletsEnum::DEFAULT->value);
            $defaultWallet->deposit($request->amount, [
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
         * @status 201
         */
        return response()->json([
            'success' => true,
            'message' => 'post created',
            'post' => $post,
        ], Response::HTTP_CREATED);

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
