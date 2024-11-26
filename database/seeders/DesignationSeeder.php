<?php

namespace Database\Seeders;

use App\Models\Departments;
use App\Models\Designations;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    protected array $names = [
        ['id' => 1, 'name' => 'Senior Developer'],
        ['id' => 2, 'name' => 'Human Resource'],
        ['id' => 3, 'name' => 'Project Manager'],
        ['id' => 4, 'name' => 'Delivery Manager'],
        ['id' => 5, 'name' => 'Quality Assurance'],
        ['id' => 6, 'name' => 'Junior Developer'],
        ['id' => 7, 'name' => 'Chief Technology Officer'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        foreach ($this->names as $name) {
            if ($name['name'] === 'Human Resource') {
                Designations::create([
                    'name' => $name['name'],
                    'departments_id' => Departments::where('name', '=', 'Admin Department')->first()->id,
                ]);

                continue;
            }
            Departments::create([
                'name' => $name['name'],
                'departments_id' => Departments::where('name', '=', 'Tech Department')->first()->id,
            ]);
        }
    }
}
