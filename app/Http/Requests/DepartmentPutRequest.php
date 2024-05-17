<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentPutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($this->id)],
            'department_head' => ['required', 'sometimes', 'exists:users,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
