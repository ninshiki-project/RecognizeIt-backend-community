<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: DepartmentSeeder.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

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
