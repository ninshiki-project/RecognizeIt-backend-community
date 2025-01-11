<?php

use App\Models\User;

it('can get all the roles', function () {
    $user = User::factory()->create();
    \Pest\Laravel\actingAs($user)->getJson('/api/v1/roles')
        ->assertStatus(200);
});
it('can show specific role by ID', function () {
    $role = \App\Models\Role::all()->random(1)->value('id');
    $user = User::factory()->create();
    \Pest\Laravel\actingAs($user)->getJson('/api/v1/roles/'.$role)
        ->assertStatus(200);
});
