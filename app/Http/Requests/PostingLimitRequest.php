<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: PostingLimitRequest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostingLimitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'designations_id' => ['required', 'exists:designations'],
            'limit' => ['required', 'integer'], //
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
