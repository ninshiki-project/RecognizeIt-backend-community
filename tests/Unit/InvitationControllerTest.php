<?php

use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('migrate:refresh --seed');
});

it('can accept invite by user', function () {
    $email = fake()->safeEmail;
    $data = \Pest\Laravel\postJson('/api/v1/users/invite', [
        'role' => \App\Models\Role::all()->random()->first()->id,
        'department' => \App\Models\Departments::first()->id,
        'email' => $email,
        'invited_by_user' => \App\Models\User::all()->random()->first()->id,
    ]);
    $payload = json_decode($data->content());
    $data->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    \Pest\Laravel\patchJson('/api/v1/invitations', [
        'name' => fake()->name,
        'email' => $email,
        'token' => $payload->data->token,
        'status' => 'accepted',
    ])->assertStatus(202)
        ->assertJson([
            'status' => 'success',
        ]);

    $this->assertDatabaseCount('invitations', 1);

});

it('can decline invite by user', function () {
    $email = fake()->safeEmail;
    $data = \Pest\Laravel\postJson('/api/v1/users/invite', [
        'role' => \App\Models\Role::all()->random()->first()->id,
        'department' => \App\Models\Departments::first()->id,
        'email' => $email,
        'invited_by_user' => \App\Models\User::all()->random()->first()->id,
    ]);
    $payload = json_decode($data->content());
    $data->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    \Pest\Laravel\patchJson('/api/v1/invitations', [
        'token' => $payload->data->token,
        'status' => 'declined',
    ])
        ->assertStatus(202)
        ->assertJson([
            'status' => 'success',
        ]);

    $this->assertDatabaseCount('invitations', 0);
});

it('can get all the invitation', function () {
    $count = 10;
    \App\Models\Invitation::factory()->accepted()->count($count)->create();
    \Pest\Laravel\getJson('/api/v1/invitations')->assertStatus(200);
    $this->assertDatabaseCount('invitations', $count);
});
