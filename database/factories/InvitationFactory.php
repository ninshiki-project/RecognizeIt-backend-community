<?php

namespace Database\Factories;

use App\Models\Departments;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class InvitationFactory extends Factory
{
    protected $model = Invitation::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'email' => $this->faker->unique()->safeEmail(),
            'invited_by_user' => User::all()->random(1)->value('id'),
            'token' => Str::random(10),
            'department' => Departments::all()->random(1)->value('id'),
            'role' => Role::all()->random(1)->value('id'),
        ];

    }

    public function accepted(): self
    {
        return $this->state(fn (array $attributes) => [
            'accepted_at' => Carbon::now(),
            'status' => 'accepted',
        ]);
    }
}
