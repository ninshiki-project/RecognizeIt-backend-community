<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: ProductsFactory.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace Database\Factories;

use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @method productName()
 */
class ProductsFactory extends Factory
{
    protected $model = Products::class;

    /**
     * @throws RandomException
     */
    public function definition(): array
    {

        return [
            /** @phpstan-ignore-next-line  */
            'name' => fake()->productName(),
            /** @phpstan-ignore-next-line  */
            'image' => fake()->placeholder(),
            'description' => $this->faker->text(),
            'price' => random_int(1000, 40000),
            'stock' => random_int(100, 300),
            'status' => collect(['available', 'unavailable'])->random(1)[0],
        ];
    }
}
