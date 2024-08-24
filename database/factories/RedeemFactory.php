<?php

namespace Database\Factories;

use App\Models\Products;
use App\Models\Redeem;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RedeemFactory extends Factory
{
    protected $model = Redeem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $shop = Shop::inRandomOrder()->first();

        return [
            'product_id' => $shop->product->id,
            'status' => $this->faker->randomElement(RedeemStatusEnum::cases())->value,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => User::inRandomOrder()->first()->id,
            'shop_id' => $shop->id,
        ];
    }
}
