<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: DatabaseSeeder.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            DesignationSeeder::class,
            TheSeeder::class,
            ProductSeeder::class,
            ShopSeeder::class,
            ReedemSeeder::class,
            PostSeeder::class,
        ]);

    }
}
