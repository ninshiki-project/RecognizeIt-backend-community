<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concern\CanPurgeCache;
use App\Http\Controllers\Api\Enum\InvitationCaseEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserInvitationRequest;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\User\Invitation\InvitationNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    use CanPurgeCache;

    protected static ?string $cacheKey = 'users';

    /**
     * Get all users
     *
     * @return JsonResponse
     */
    public function index()
    {
        return Cache::remember(static::$cacheKey, Carbon::now()->addDays(5), function () {
            return response()->json(User::all());
        });
    }

    /**
     * Invite User
     *
     * @return JsonResponse
     */
    public function inviteUser(UserInvitationRequest $request)
    {
        $token = base64_encode(json_encode([
            'email' => $request->email,
            'invitation_by_user' => $request->invited_by_user,
        ]));
        $user = User::findOrFail($request->invited_by_user);
        $invitation = Invitation::create([
            'invited_by_user' => $user?->id,
            'department' => $request->department,
            'role' => $request->role,
            'email' => $request->email,
            'token' => $token,
            'status' => InvitationCaseEnum::Pending,
        ]);

        // Send an email invitation
        Notification::route('mail', $request->email)
            ->notify(new InvitationNotification($user, $invitation));

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
            ],
            'message' => 'Invitation sent via email.',
        ]);
    }

    /**
     * Get User by ID
     *
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        return Cache::remember(static::$cacheKey.$id, Carbon::now()->addDays(5), function () use ($id) {
            return response()->json(User::findOrFail($id));
        });

    }

    /**
     * Delete User
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /**
         * Removed Cache
         */
        $this->purgeCache();
        $this->purgeCache(static::$cacheKey.$id);

        return response()->noContent();
    }
}
