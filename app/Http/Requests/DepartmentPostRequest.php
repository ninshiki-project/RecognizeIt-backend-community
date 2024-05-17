<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentPostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
            'department_head' => ['required', 'sometimes', 'exists:users,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
