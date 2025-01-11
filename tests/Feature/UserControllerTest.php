<?php

use App\Models\User;

it('can get all the user', function () {
    $user = User::factory()->create();
    \Pest\Laravel\actingAs($user)->getJson('/api/v1/users')->assertStatus(200);
});

it('can show all user', function () {
    $user = User::factory()->create();
    \Pest\Laravel\actingAs($user)->getJson('/api/v1/users')->assertStatus(200);
});
