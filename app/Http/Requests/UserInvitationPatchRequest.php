<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserInvitationPatchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'sometimes', 'required_if:status,accepted'],
            'email' => ['required',  'email', 'max:254', 'sometimes', 'required_if:status,accepted'],
            'token' => ['required', 'exists:invitations,token'],
            'status' => ['required', Rule::in(['accepted', 'declined'])],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
