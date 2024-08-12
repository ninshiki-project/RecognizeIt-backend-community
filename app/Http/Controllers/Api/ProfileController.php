<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileResetPasswordRequest;
use App\Http\Requests\ProfileUpdatePasswordRequest;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    /**
     * Session Profile
     *
     * @return ProfileResource
     */
    public function me()
    {
        return new ProfileResource(auth()->user());
    }

    /**
     * Update Password
     *
     * @return JsonResponse
     */
    public function changePassword(ProfileUpdatePasswordRequest $request)
    {
        auth()->user()->update(['password' => Hash::make($request->password)]);

        /**
         * @status 202
         */
        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Request Forgot Password Email
     *
     *
     * @return JsonResponse
     *
     * @unauthenticated
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);
        // Check if the user who used the email doesn't use the Zoho login
        $user = User::where('email', $request->email)->firstOrFail();
        if ($user->password) {
            // send email for password reset
            $status = Password::sendResetLink($request->only('email'));
            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'success' => true,
                    'message' => __($status),
                ]);
            }
            if ($status === Password::INVALID_USER) {
                return response()->json([
                    'success' => false,
                    'message' => __($status),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            if ($status === Password::RESET_THROTTLED) {
                return response()->json([
                    'success' => false,
                    'message' => __($status),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        // send email for notification that the user need to login via Zoho instead of credential
        return response()->json([
            'success' => false,
            'message' => 'Your email address was not found.',
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Reset Password
     *
     *
     * @return JsonResponse|void
     *
     * @unauthenticated
     */
    public function resetPassword(ProfileResetPasswordRequest $request)
    {

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => __($status),
            ]);
        }
        if ($status === Password::INVALID_TOKEN) {
            return response()->json([
                'success' => false,
                'message' => __($status),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

    }
}
