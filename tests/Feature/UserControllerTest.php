<?php

it('can get all the user', function () {
    \Pest\Laravel\getJson('/api/v1/users')->assertStatus(200);
});

it('can invite new user', function () {
    \Pest\Laravel\postJson('/api/v1/users/invite', [
        'role' => \App\Models\Role::all()->random()->first()->id,
        'department' => \App\Models\Departments::first()->id,
        'email' => fake()->safeEmail,
        'added_by' => \App\Models\User::first()->id,
    ])
        ->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);
});

it('can show all user', function () {
    \Pest\Laravel\getJson('/api/v1/users')->assertStatus(200);
});
