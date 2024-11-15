<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;

it('can change password', function () {
    $email = fake()->safeEmail();
    $newPassword = 'Newpassword!@1!';
    $user = \App\Models\User::factory()->create([
        'email' => $email,
    ]);

    $resp = \Pest\Laravel\patchJson('/api/v1/auth/change-password', [
        'current_password' => 'password',
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ])->assertStatus(202)
        ->assertJson([
            'success' => true,
        ]);

});

it('can retrieve the session profile of the user', function () {
    \Pest\Laravel\getJson('/api/v1/auth/me')
        ->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => $json->has('data')
        );
});
