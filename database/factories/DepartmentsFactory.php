<?php

namespace Database\Factories;

use App\Models\Departments;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DepartmentsFactory extends Factory
{
    protected $model = Departments::class;

    public function definition(): array
    {
        $coll = collect([
            ['name' => 'Tech'],
            ['name' => 'PM'],
            ['name' => 'Admin'],
        ])->random(1);

        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'name' => $coll->all()[0]['name'],
            'department_head' => User::all()->random(1)->first()->id,
        ];
    }
}
