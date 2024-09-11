<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserInvitationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'role' => ['required', 'exists:roles,id'],
            'department' => ['required', 'exists:departments,id'],
            'email' => ['required',  'email', 'max:254',
                Rule::unique('users', 'email'),
            ],
            'added_by' => ['required', 'exists:users,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
