<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserInvitationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required',  'email', 'max:254',
                Rule::unique('users', 'email'),
                Rule::unique('invitation', 'email'),
            ],
            'invited_by_user' => ['required', 'exists:users'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
