<?php

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
