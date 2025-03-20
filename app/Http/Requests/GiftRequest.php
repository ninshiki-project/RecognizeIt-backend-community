<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: GiftRequest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Requests;

use App\Enum\GiftEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GiftRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // User ID. If NULL, the authenticated login user will be used.
            'sender' => ['nullable', 'exists:users'],
            // User ID
            'receiver' => ['required', 'exists:users'],
            'type' => ['required', Rule::enum(GiftEnum::class)->only([GiftEnum::SHOP, GiftEnum::COINS])],
            // This only required if the type is shop
            'shop' => ['nullable', 'exists:shops,id', 'required_if:type,'.GiftEnum::SHOP->value],
            // This only required if the type is coins
            'amount' => ['nullable', 'numeric', 'min:1', 'required_if:type,'.GiftEnum::COINS->value],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
