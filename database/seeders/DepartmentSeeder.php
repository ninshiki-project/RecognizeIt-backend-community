<?php

namespace Database\Seeders;

use App\Models\Departments;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    protected array $departments = [
        ['name' => 'Tech Department'],
        ['name' => 'Admin Department'],
        ['name' => 'Sales Department'],
    ];

    public function run(): void
    {
        foreach ($this->departments as $department) {
            Departments::create([
                'name' => $department['name'],
            ]);
        }

    }
}
