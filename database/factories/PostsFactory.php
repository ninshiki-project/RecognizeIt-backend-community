<?php

namespace Database\Factories;

use App\Models\Posts;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PostsFactory extends Factory
{
    protected $model = Posts::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'content' => $this->faker->word(),
            'image' => $this->faker->word(),
            'type' => $this->faker->word(),
            'posted_by' => $this->faker->word(),
        ];
    }
}
