<?php

namespace App\Http\Controllers\Api;

use App\Events\LogoutOtherBrowser;
use App\Http\Controllers\Api\Concern\Agent;
use App\Http\Controllers\Api\Concern\CanLogoutOtherDevices;
use App\Http\Controllers\Controller;
use App\Http\Requests\LogOutOtherBrowserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                'is_current_device' => $session->id == \Str::of(auth()->user()->currentAccessToken()->id)->explode('|')[0],
                'last_active' => $session->last_used_at ? Carbon::createFromDate($session->last_used_at)->diffForHumans() : 'Unknown',
            ];
        });

        return response()->json($data->toArray(), Response::HTTP_OK);
    }

    /**
     *  Logout Other Browser Session
     */
    public function logoutOtherDevices(LogOutOtherBrowserRequest $request)
    {

        $this->logoutOtherDevicesSession($request);

        LogoutOtherBrowser::dispatch($request->user());
    }

    /**
     * Session Health
     *
     * This route is used to check if the session with the backend is still healthy, and it has not been logout from other device this will check via Sanctum token
     */
    public function health()
    {
        return response()->json('', Response::HTTP_OK);
    }

    /**
     * Create a new agent instance from the given session.
     *
     * @param  mixed  $session
     * @return Agent
     */
    protected function createAgent(mixed $session)
    {
        return tap(new Agent, fn ($agent) => $agent->setUserAgent($session->name));
    }
}
