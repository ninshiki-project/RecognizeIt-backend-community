<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: DesignationsFactory.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace Database\Factories;

use App\Models\Departments;
use App\Models\Designations;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DesignationsFactory extends Factory
{
    protected $model = Designations::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => '',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'departments_id' => Departments::factory(),
        ];
    }
}
