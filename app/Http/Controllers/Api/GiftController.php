<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GiftController extends Controller
{
    /**
     * Enable Gift Feature
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
}
