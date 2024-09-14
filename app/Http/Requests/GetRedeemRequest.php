<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: GetRedeemRequest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Requests;

use App\Http\Controllers\Api\Enum\RedeemStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetRedeemRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // Allowed format: array|integer
            'user' => [
                'sometimes',
                'exists:users,id',
                'integer',
            ],
            'user.*' => [
                'sometimes',
                'exists:users,id',
                'integer',
            ],
            'status' => [
                'sometimes',
                Rule::enum(RedeemStatusEnum::class),
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
