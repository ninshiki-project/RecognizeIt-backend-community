<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserInvitationPatchRequest;
use App\Models\Invitation;
use App\Models\User;
use Carbon\Carbon;

class UserInvitationController extends Controller
{
    /**
     * Accept Invitation
     */
    public function invitation(UserInvitationPatchRequest $request)
    {
        // get Invitation data
        $invitation = Invitation::where('token', $request->token)->firstOrFail();

        if ($request->status === 'declined') {
            $invitation->delete();
            // Send email to the user who invite that the invitation has been declined by the recipient

            return response()->json([
                'status' => 'success',
                'message' => 'Invitation declined',
            ], 202);
        }

        // create into user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'department' => $invitation?->department,
            'role' => $invitation?->role,
        ])->save();

        $user->points()->create();

        // update the invitation
        $invitation->status = 'accepted';
        $invitation->accepted_at = Carbon::now();
        $invitation->token = null;
        $invitation->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Invitation accepted',
        ], 202);

    }
}
