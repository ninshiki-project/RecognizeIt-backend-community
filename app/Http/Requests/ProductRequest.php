<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: ProductRequest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Requests;

use App\Http\Controllers\Api\Enum\ProductStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->method() === 'POST') {
            return $this->rulesForCreation();
        } else {
            return $this->rulesForUpdate();
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    protected function rulesForCreation(): array
    {
        return [
            'name' => ['required', 'string'],
            'price' => ['required', 'integer', 'min:5'],
            'description' => ['sometimes', 'string', 'max:255'],
            'image' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
            'stock' => ['required', 'integer', 'min:1'],
        ];
    }

    protected function rulesForUpdate(): array
    {
        return [
            'name' => ['sometimes', 'string'],
            'price' => ['sometimes', 'integer', 'min:5'],
            'description' => ['sometimes', 'string', 'max:255'],
            'image' => ['sometimes', 'file', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
            'stock' => ['sometimes', 'integer', 'min:1'],
            'status' => ['sometimes', Rule::enum(ProductStatusEnum::class)],
        ];
    }
}
