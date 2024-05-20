<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ProfileUpdatePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'confirmed',
                Password::min(8)->mixedCase()->numbers()
                    ->symbols()->uncompromised(3),
            ],
            'password_confirmation' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
