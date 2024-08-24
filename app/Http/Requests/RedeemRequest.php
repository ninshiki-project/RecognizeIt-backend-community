<?php

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
