<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginViaEmailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required|sometimes|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
