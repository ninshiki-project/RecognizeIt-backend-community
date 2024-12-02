<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: RedeemRequest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RedeemRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'shop_id' => 'required|exists:shops,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
