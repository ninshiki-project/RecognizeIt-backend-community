<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: RedeemFactory.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace Database\Factories;

use App\Http\Controllers\Api\Enum\RedeemStatusEnum;
use App\Models\Redeem;
use App\Models\Shop;
use App\Models\User;
use Bavix\Wallet\Internal\Exceptions\ExceptionInterface;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RedeemFactory extends Factory
{
    protected $model = Redeem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    public function definition(): array
    {
        $shop = Shop::inRandomOrder()->first();
        $user = User::first();
        $user->wallet->deposit(400000);
        $user->pay($shop->product);

        return [
            'product_id' => $shop->product->id,
            'status' => $this->faker->randomElement(RedeemStatusEnum::cases())->value,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => $user->id,
            'shop_id' => $shop->id,
        ];
    }
}
