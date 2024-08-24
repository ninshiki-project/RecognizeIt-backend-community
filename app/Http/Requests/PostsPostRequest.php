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
            'recipient_id' => ['required', 'array'],
            'recipient_id.*' => ['required', 'exists:users,id'],
            'post_content' => ['required', 'string'],
            'amount' => ['required_if:type,user', 'integer', 'in:3,5,10'],
            'attachment_type' => ['required_if:type,user', Rule::in(['gif', 'image']), 'sometimes'],
            'gif_url' => ['required_if:attachment_type,gif', 'url', 'sometimes'],
            'image' => ['required_if:attachment_type,image', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048', 'sometimes'],
            'type' => ['required', Rule::enum(PostTypeEnum::class)],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
