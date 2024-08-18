<?php

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
