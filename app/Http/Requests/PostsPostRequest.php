<?php

namespace App\Http\Requests;

use App\Http\Controllers\Api\Enum\PostTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostsPostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'recipient_id' => ['required', 'array', 'exists:users,id'],
            'content' => ['required'],
            'image' => ['required', 'sometimes'],
            'type' => ['required', Rule::enum(PostTypeEnum::class)],
            'posted_by' => ['required', 'exists:users,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
