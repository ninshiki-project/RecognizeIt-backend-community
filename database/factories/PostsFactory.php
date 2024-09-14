<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: PostsFactory.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace Database\Factories;

use App\Models\Posts;
use App\Models\User;
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
            'content' => $this->faker->words(asText: true),
            'attachment_url' => $this->faker->imageUrl(),
            'type' => 'user',
            'attachment_type' => 'gif',
            'posted_by' => User::first()->id,
        ];
    }
}
