<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: DepartmentPostRequest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

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
