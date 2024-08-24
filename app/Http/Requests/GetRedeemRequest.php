<?php

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
