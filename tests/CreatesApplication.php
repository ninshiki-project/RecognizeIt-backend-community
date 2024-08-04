<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Laravel\Sanctum\Sanctum;

trait CreatesApplication
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');

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
