<?php

namespace Database\Factories;

use App\Models\Invitation;
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
            'invited_by' => $this->faker->word(),
            'token' => Str::random(10),
            'accepted_at' => Carbon::now(),
            'status' => $this->faker->word(),
            'declined_at' => Carbon::now(),
        ];
    }
}
