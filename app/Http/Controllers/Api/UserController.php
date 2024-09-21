<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: UserController.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Enum\UserEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserInvitationPatchRequest;
use App\Http\Requests\UserInvitationRequest;
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
use Illuminate\Support\Str;
use MarJose123\NinshikiEvent\Events\User\UserAdded;
use MarJose123\NinshikiEvent\Events\User\UserDeleted;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    protected static ?string $cacheKey = 'users';

    /**
     * Get all users
     *
     * @return LengthAwarePaginator<User>
     */
    public function index()
    {
        return Cache::flexible(static::$cacheKey, [5, 10], function () {
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
            $whoUser->notify(new DeclinedNotification($whoUser, $invitedUser));
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
     *
     * @param  UserInvitationRequest  $request
     * @return JsonResponse
     */
    public function inviteUser(UserInvitationRequest $request): JsonResponse
    {
        $token = base64_encode(json_encode([
            'email' => $request->email,
            'added_by' => $request->added_by,
        ]));
        $user = User::findOrFail($request->added_by);
        $roles = Role::findById($request->role);
        $name = Str::replace('.', ' ', Str::of($request->email)->before('@'));
        $name = Str::ucfirst($name);
        $invitation = User::create([
            'name' => $name,
            'added_by' => $user->id,
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
     * @param  string  $id
     * @return JsonResponse
     */
    public function show(string $id)
    {
        return Cache::flexible(static::$cacheKey.$id, [5, 10], function () use ($id) {
            return response()->json(User::findOrFail($id));
        });

    }

    /**
     * Delete User
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {

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
