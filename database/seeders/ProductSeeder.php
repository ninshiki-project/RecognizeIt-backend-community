<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: ProductSeeder.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace Database\Seeders;

use App\Models\Products;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isLocal() || app()->runningUnitTests()) {
            Products::factory()->count(250)->create();
        }
    }
}
