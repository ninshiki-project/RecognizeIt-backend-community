<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GiftRequest;
use App\Http\Resources\GiftResource;
use App\Models\Application;
use App\Models\Gift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * Send Gift to other Employee
     *
     * @param  GiftRequest  $request
     * @return void
     */
    public function store(GiftRequest $request)
    {
        // check if the feature is enabled or not.
        if (! Application::first()->more_configs['gift']['enable']) {
            throw ValidationException::withMessages([
                'error' => 'Gift feature is not enable in the system.',
            ]);
        }

    }
}
