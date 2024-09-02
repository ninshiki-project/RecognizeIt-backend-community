<?php

namespace Database\Factories;

use App\Models\Recipients;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class recipientsFactory extends Factory
{
    protected $model = Recipients::class;

    public function definition(): array
    {

        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
