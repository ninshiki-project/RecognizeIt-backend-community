<?php

namespace Database\Seeders;

use App\Models\Posts;
use App\Models\Recipients;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        if (app()->isLocal()) {
            Posts::factory()
                ->has(Recipients::factory(3), 'recipients')
                ->count(50)->create();
        }
    }
}
