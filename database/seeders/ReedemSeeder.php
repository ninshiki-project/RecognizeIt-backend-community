<?php

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
