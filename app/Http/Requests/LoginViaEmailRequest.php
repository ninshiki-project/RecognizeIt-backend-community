<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: LoginViaEmailRequest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginViaEmailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required|sometimes|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
