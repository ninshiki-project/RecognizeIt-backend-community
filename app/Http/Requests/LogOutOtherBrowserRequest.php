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
use Illuminate\Validation\Rule;

class LogOutOtherBrowserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            /**
             * Only required to be passed on the request if the user has a permission "access panel"
             */
            'password' => [
                Rule::requiredIf(fn () => auth()->user()->can('access panel')),
                'nullable',
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
