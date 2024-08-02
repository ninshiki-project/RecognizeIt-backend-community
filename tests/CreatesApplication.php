<?php

namespace Tests;

use App\Models\User;
use Laravel\Sanctum\Sanctum;

trait CreatesApplication
{
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

    }
}
