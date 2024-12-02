<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: ProfileUpdatePasswordRequest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ProfileUpdatePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password:sanctum'],
            'password' => ['required', 'confirmed',
                Password::min(8)->mixedCase()->numbers()
                    ->symbols()->uncompromised(3),
            ],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
