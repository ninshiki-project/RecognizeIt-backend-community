<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: ReedemSeeder.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace Database\Seeders;

use App\Models\Redeem;
use Illuminate\Database\Seeder;

class ReedemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        if (app()->runningUnitTests()) {
            Redeem::factory(100)->create();
        }
    }
}
