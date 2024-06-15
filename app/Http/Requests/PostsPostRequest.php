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
            'content' => ['required'],
            'points' => ['required_if:type,user', 'integer'],
            'attachment_type' => ['required_if:type,user', Rule::in(['gif', 'image'])],
            'gif' => ['required_if:attachment_type,gif'],
            'image' => ['required_if:attachment_type,image', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'type' => ['required', Rule::enum(PostTypeEnum::class)],
            'posted_by' => ['required', 'exists:users,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
