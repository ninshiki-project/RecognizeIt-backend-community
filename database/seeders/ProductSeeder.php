<?php

namespace Database\Seeders;

use App\Models\Products;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment() !== 'production') {
            Products::factory()->count(200)->create();
        }
    }
}
