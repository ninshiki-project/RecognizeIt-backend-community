<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
