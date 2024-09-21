<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: SessionController.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api;

use App\Events\Broadcast\LogoutSessionEvent;
use App\Http\Controllers\Api\Concern\Agent;
use App\Http\Controllers\Api\Concern\CanLogoutOtherDevices;
use App\Http\Controllers\Controller;
use App\Http\Requests\LogOutOtherBrowserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use MarJose123\NinshikiEvent\Events\Session\LogoutOtherBrowser;
use Symfony\Component\HttpFoundation\Response;

class SessionController extends Controller
{
    use CanLogoutOtherDevices;

    /**
     * Sessions
     *
     * Get the all user login sessions.
     *
     * @return JsonResponse
     *
     * @response array JsonResponse
     */
    public function loginSessions(): JsonResponse
    {
        $data = collect(
            DB::connection(config('database.default'))->table('personal_access_tokens')
                ->where('tokenable_id', Auth::user()->id)
                ->orderBy('last_used_at', 'desc')
                ->get()
        )->map(function ($session) {
            return (object) [
                'platform' => $this->createAgent($session)->platform(),
                'browser' => $this->createAgent($session)->browser(),
                'is_desktop' => $this->createAgent($session)->isDesktop(),
                // @phpstan-ignore-next-line
                'is_current_device' => $session->id == \Str::of(auth()->user()->currentAccessToken()->id)->explode('|')[0],
                'last_active' => $session->last_used_at ? Carbon::createFromDate($session->last_used_at)->diffForHumans() : 'Unknown',
            ];
        });

        return response()->json($data->toArray(), Response::HTTP_OK);
    }

    /**
     *  Logout Other Device Session
     */
    public function logoutOtherDevices(LogOutOtherBrowserRequest $request): void
    {

        $this->logoutOtherDevicesSession($request);

        // Send Broadcast Event
        LogoutSessionEvent::dispatch(auth()->user());
        /**
         * Dispatch an event
         */
        LogoutOtherBrowser::dispatch($request->user());
    }

    /**
     *  Session Health
     *
     *  This route is used to check if the session with the backend is still healthy, and it has not been logout from other device this will check via Sanctum token
     *
     * @return JsonResponse
     */
    public function health(): JsonResponse
    {
        return response()->json('', Response::HTTP_OK);
    }

    /**
     * Create a new agent instance from the given session.
     *
     * @param  mixed  $session
     * @return Agent
     */
    protected function createAgent(mixed $session): Agent
    {
        return tap(new Agent, fn ($agent) => $agent->setUserAgent($session->name));
    }
}
