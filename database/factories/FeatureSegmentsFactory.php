<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: FeatureSegmentsFactory.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace Database\Factories;

use App\Models\FeatureSegments;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class FeatureSegmentsFactory extends Factory
{
    protected $model = FeatureSegments::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'feature' => $this->faker->word(), //
            'scope' => $this->faker->word(),
            'values' => $this->faker->words(),
            'active' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
