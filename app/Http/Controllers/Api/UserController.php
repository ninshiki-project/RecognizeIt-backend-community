<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concern\CanPurgeCache;
use App\Http\Controllers\Api\Enum\UserEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserInvitationPatchRequest;
use App\Http\Requests\UserInvitationRequest;
use App\Http\Resources\PostResource;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use App\Notifications\User\Invitation\DeclinedNotification;
use App\Notifications\User\Invitation\InvitationNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use MarJose123\NinshikiEvent\Events\User\UserAdded;
use MarJose123\NinshikiEvent\Events\User\UserDeleted;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use CanPurgeCache;

    protected static ?string $cacheKey = 'users';

    /**
     * Get all users
     *
     * @return LengthAwarePaginator<User>
     */
    public function index()
    {
        return Cache::remember(static::$cacheKey, Carbon::now()->addDays(5), function () {
            return User::paginate();
        });
    }

    /**
     *  Get All Invitation
     *
     * @return LengthAwarePaginator<User>
     */
    public function getAllInvitations()
    {
        return User::invitedStatus()->paginate();
    }

    /**
     * Resend Invitation
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function resendInvitation(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $whoUser = User::where('email', $request->email)->firstOrFail();
        $fromUser = User::find($whoUser->added_by);

        if ($whoUser->status !== UserEnum::Invited) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already a user',
            ], Response::HTTP_BAD_REQUEST);
        }
        // Send an email invitation
        Notification::route('mail', $request->email)
            ->notify(new InvitationNotification($fromUser, $whoUser));

        return response()->json([
            'success' => true,
            'message' => 'Email invitation has been sent successfully.',
        ], Response::HTTP_OK);

    }

    /**
     * Accept/Decline Invitation
     *
     * @param  UserInvitationPatchRequest  $request
     * @return JsonResponse
     */
    public function invitation(UserInvitationPatchRequest $request)
    {
        // get Invitation data
        $invitedUser = User::where('invitation_token', $request->token)
            ->where('email', $request->email)
            ->firstOrFail();

        if ($request->status === 'declined') {
            $whoUser = User::findOrFail($invitedUser->added_by);
            // Send email notification to the user who invited,
            //that the invited person has declined accepting the invitation to join
            $whoUser?->notify(new DeclinedNotification($whoUser, $invitedUser));
            $invitedUser->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Invitation declined',
            ], Response::HTTP_ACCEPTED);
        }
        // update the user
        $invitedUser->update([
            'status' => UserEnum::Active,
            'password' => bcrypt($request->password),
            'email_verified_at' => Carbon::now(),
            'invitation_token' => null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Invitation accepted',
        ], Response::HTTP_ACCEPTED);

    }

    /**
     * Invite User
     *
     * The system will handle for sending invitation email
     *
     * @return JsonResponse
     */
    public function inviteUser(UserInvitationRequest $request)
    {
        $token = base64_encode(json_encode([
            'email' => $request->email,
            'added_by' => $request->added_by,
        ]));
        $user = User::findOrFail($request->invited_by_user);
        $roles = Role::findById($request->role);
        $invitation = User::create([
            'added_by' => $user?->id,
            'department' => $request->department,
            'email' => $request->email,
            'token' => $token,
            'status' => UserEnum::Invited,
        ])->assignRole($roles->name);

        // Send an email invitation
        Notification::route('mail', $request->email)
            ->notify(new InvitationNotification($user, $invitation));

        /**
         * Dispatch event for invited user
         */
        UserAdded::dispatch($invitation);

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

        $user = User::findOrFail($id);

        /**
         * Dispatch event
         */
        UserDeleted::dispatch($user);

        /**
         * Delete user
         */
        $user->delete();

        return response()->noContent();
    }
}
