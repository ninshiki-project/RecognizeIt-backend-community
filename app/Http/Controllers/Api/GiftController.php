<?php

namespace App\Http\Controllers\Api;

use App\Enum\GiftEnum;
use App\Enum\WalletsEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\GiftRequest;
use App\Http\Resources\GiftResource;
use App\Models\Application;
use App\Models\Gift;
use App\Models\User;
use Bavix\Wallet\Internal\Exceptions\ExceptionInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class GiftController extends Controller
{
    /**
     * Enable Gift Feature
     *
     * This is used to enable/disable the Gift feature before be able to use Gift APIs.
     *
     * @return JsonResponse
     */
    public function enable(Request $request)
    {
        $request->validate([
            'enable' => 'required|boolean',
            'limit_count' => 'required|numeric|min:1',
            'frequency' => 'required|in:weekly,monthly,yearly',
        ]);

        $application = Application::first();
        $application->forceFill([
            'more_configs->gift' => [
                'enable' => $request->enable,
                'limit_count' => $request->limit_count,
                'frequency' => $request->frequency,
            ],
        ])->update();

        $message = sprintf('Gift feature %s successfully', $request->enable === true ? 'enabled' : 'disabled');

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Get All Gift
     *
     *
     * @param  Request  $request
     * @return mixed
     */
    public function index(Request $request)
    {

        $request->validate([
            /** @query */
            'per_page' => 'integer|min:200',
            /** @query */
            'page' => 'integer|min:1',
        ]);
        $cacheKey = sprintf('gift.list.pp-%s.p-%d', $request->page, $request->per_page);

        return Cache::flexible($cacheKey, [5, 15], function () {
            return GiftResource::collection(
                Gift::orderByDesc('created_at')
                    ->paginate(
                        perPage: $request?->per_page ?? 200,
                        page: $request?->page ?? 1)
            );
        });
    }

    /**
     * Send Gift to Employee/User
     *
     * This transaction is not reversible
     *
     * @param  GiftRequest  $request
     * @return JsonResponse
     *
     * @throws ExceptionInterface
     */
    public function store(GiftRequest $request)
    {
        // check if the feature is enabled or not.
        if (! Application::first()->more_configs['gift']['enable']) {
            throw ValidationException::withMessages([
                'error' => 'Gift feature is not enable in the system.',
            ]);
        }
        // Temporary disable the shop gifting
        if ($request->type === GiftEnum::SHOP) {
            throw ValidationException::withMessages([
                'type' => 'Gifting shop item is temporarily disabled.',
            ]);
        }

        $exchangeRate = Application::first()->more_configs['gift']['exchange_rate'] ?? 1;
        $convertedAmount = $request->amount * $exchangeRate;

        $giftRecord = Gift::create([
            'type' => $request->type,
            'amount' => $request->amount,
            'to' => $request->to,
            'by' => $request->by,
        ]);

        $recipientUser = User::find($request->to)->first();
        $recipientWallet = $recipientUser->getWallet(WalletsEnum::DEFAULT->value);
        $recipientWallet->deposit($convertedAmount, [
            'title' => 'Ninshiki Wallet',
            'model' => [
                'model' => Gift::class,
                'record' => $giftRecord,
            ],
            'description' => 'One of your colleague sent you a coins as a gift.',
            'date_at' => Carbon::now(),
        ]);

        $senderUser = User::find($request->by)->first();
        $senderWallet = $senderUser->getWallet(WalletsEnum::SPEND->value);
        $senderWallet->withdraw($request->amount, [
            'title' => 'Spend Wallet',
            'model' => [
                'model' => Gift::class,
                'record' => $giftRecord,
            ],
            'description' => 'You sent a coins to your colleague as a gift.',
            'date_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gift has been sent successfully.',
        ]);

    }
}
