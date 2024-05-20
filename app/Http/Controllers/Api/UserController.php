<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserInvitationRequest;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\User\Invitation\InvitationNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    /**
     * Get all users
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(User::all());
    }

    /**
     * Invite User
     *
     * @return JsonResponse
     */
    public function inviteUser(UserInvitationRequest $request)
    {
        $token = base64_encode((object) [
            'email' => $request->email,
            'invitation_by_user' => $request->invited_by_user,
        ]);
        $user = User::findOrFail($request->invited_by_user);
        $invitation = Invitation::create([
            'invited_by_user' => $user?->id,
            'department' => $request->department,
            'role' => $request->role,
            'email' => $request->email,
            'token' => $token,
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
        return response()->json(User::findOrFail($id));
    }

    /**
     * Delete User
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        return response()->json(User::findOrFail($id)->delete(), 202);
    }

    /**
     * Get Points of User
     *
     * @return JsonResponse
     */
    public function showPoints(User $user)
    {
        return response()->json($user->points);
    }

    /**
     * My Authenticated Profile
     *
     * @return JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user()->load(['roles.permissions', 'notifications']));
    }
}
