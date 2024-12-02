<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: UserInvitationPatchRequest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserInvitationPatchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'sometimes', 'required_if:status,accepted'],
            'email' => ['required',  'email', 'max:254', 'sometimes', 'required_if:status,accepted'],
            'token' => ['required', 'exists:invitations,token'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8', 'same:password'],
            'status' => ['required', Rule::in(['accepted', 'declined'])],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
