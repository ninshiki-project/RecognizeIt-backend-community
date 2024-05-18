<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserInvitationPatchRequest;
use App\Http\Requests\UserInvitationRequest;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * @param UserInvitationRequest $request
     * @return JsonResponse
     */
    public function inviteUser(UserInvitationRequest $request)
    {
        $token = base64_encode((object) [
            'email' => $request->email,
            'invitation_by_user' => $request->invited_by_user,
        ]);
        User::findOrFail($request->invited_by_user)->invitations()->create([
            'email' => $request->email,
            'token' => $token,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
            ],
            'message' => 'Invitation sent via email.',
        ]);
    }

    /**
     * Accept Invitation
     */
    public function invitation(User $user, UserInvitationPatchRequest $request)
    {

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
     * Update User
     *
     * @param Request $request
     * @param $id
     * @return void
     */
    public function update(Request $request, $id)
    {
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
     * Get all Points of User
     *
     * @return JsonResponse
     */
    public function showPoints(User $user)
    {
        return response()->json($user->points);
    }
}
