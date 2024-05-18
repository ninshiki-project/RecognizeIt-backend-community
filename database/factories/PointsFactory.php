<?php

namespace Database\Factories;

use App\Models\Points;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PointsFactory extends Factory
{
    protected $model = Points::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => $this->faker->word(),
            'points_earned' => $this->faker->randomNumber(),
            'credits' => $this->faker->word(),
        ];
    }
}
