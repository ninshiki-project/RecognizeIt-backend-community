<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: LogOutOtherBrowserRequest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogOutOtherBrowserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'current_password',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        return [
            ...parent::messages(),
            'current_password' => 'This password does not match our records.',
        ];
    }
}
