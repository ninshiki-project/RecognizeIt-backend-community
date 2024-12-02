<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: PostSeeder.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace Database\Seeders;

use App\Models\Posts;
use App\Models\Recipients;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        if (app()->isLocal() || app()->runningUnitTests()) {
            Posts::factory()
                ->has(Recipients::factory(3), 'recipients')
                ->count(50)->create();
        }
    }
}
