<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserInvitationPatchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required',  'email', 'max:254'],
            'invited_by_user' => ['required', 'exists:users'],
            'token' => ['required', Rule::exists('invitation', 'token')],
            'status' => ['required', Rule::in(['accepted', 'declined'])],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
