<?php

it('can get all the user', function () {
    \Pest\Laravel\getJson('/api/v1/users')->assertStatus(200);
});

it('can invite new user', function () {
    \Pest\Laravel\postJson('/api/v1/users/invite', [
        'role' => \App\Models\Role::all()->random()->first()->id,
        'department' => \App\Models\Departments::first()->id,
        'email' => fake()->safeEmail,
        'invited_by_user' => \App\Models\User::all()->random()->first()->id,
    ])
        ->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);
});

it('can show points of specific user', function () {
    $content = \Pest\Laravel\getJson('api/v1/users/1/points')->content();
    expect(json_decode($content))->toMatchObject([
        'id' => 1,
        'user_id' => 1,
        'points_earned' => 0,
        'credits' => 30,
    ]);
});

it('can show all user', function () {
    \Pest\Laravel\getJson('/api/v1/users')->assertStatus(200);
});
