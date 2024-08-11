<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogOutOtherBrowserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'current_password',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        return [
            ...parent::messages(),
            'current_password' => 'This password does not match our records.',
        ];
    }
}
