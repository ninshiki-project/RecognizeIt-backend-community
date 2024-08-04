<?php

namespace Tests;

use App\Models\User;
use Laravel\Sanctum\Sanctum;

trait CreatesApplication
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:refresh --seed');

        $user = User::factory()
            ->create();
        $user->points()->create([
            'user_id' => $user->id,
        ]);
        Sanctum::actingAs(
            $user,
            ['*']
        );

    }
}
