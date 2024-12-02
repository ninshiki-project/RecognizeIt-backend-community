<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: GetProductRequest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => [
                'sometimes',
                'string',
                'in:available,unavailable',
            ],
            'page' => [
                'sometimes',
                'integer',
            ],
            'per_page' => [
                'sometimes',
                'integer',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
