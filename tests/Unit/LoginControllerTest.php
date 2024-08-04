<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('can login via email', function () {
    \Pest\Laravel\postJson('/api/login/credentials', [
        'email' => User::find(1)->email,
        'password' => 'password',
        'device_name' => 'pest',
    ])->assertStatus(200);
});

test('can logout', function () {
    Sanctum::actingAs(
        User::factory()->create(),
        ['*']
    );

    \Pest\Laravel\postJson('/api/logout', headers: [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->assertStatus(202);

});
