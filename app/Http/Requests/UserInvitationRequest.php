<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: UserInvitationRequest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

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
