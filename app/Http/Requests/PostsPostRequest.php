<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: PostsPostRequest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Requests;

use App\Http\Controllers\Api\Enum\PostTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class PostsPostRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'recipient_id' => ['required', 'array'],
            'recipient_id.*' => ['required', 'exists:users,id'],
            'post_content' => ['required', 'string'],
            'amount' => ['required_if:type,user', 'integer', 'in:3,5,10'],
            'attachment_type' => [Rule::in(['gif', 'image']), 'sometimes'],
            'gif_url' => ['required_if:attachment_type,gif', 'url', 'sometimes'],
            'image' => ['required_if:attachment_type,image', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048', 'sometimes'],
            'type' => ['required', Rule::enum(PostTypeEnum::class)],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rateLimitValidate(): void
    {
        if (RateLimiter::tooManyAttempts($this->rateLimitKey(), maxAttempts: 1)) {
            $seconds = RateLimiter::availableIn($this->rateLimitKey());
            throw new TooManyRequestsHttpException("You're to fast in posting. please try again after ".$seconds);
        }

    }

    public function rateLimitIncrease(): void
    {
        $decayInMinutes = 5 * 60; // 5 minutes
        RateLimiter::increment($this->rateLimitKey(), $decayInMinutes);
    }

    protected function rateLimitKey(): string
    {
        return 'post-created:'.$this->user()->id;
    }
}
