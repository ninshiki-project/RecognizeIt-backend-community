<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: DesignationsRequest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DesignationsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'departments_id' => ['required', 'exists:departments'], //
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
