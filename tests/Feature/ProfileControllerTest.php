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
it('can request forgot password email', function () {
    Notification::fake();
    $email = fake()->safeEmail();
    $user = \App\Models\User::factory()->create([
        'email' => $email,
        'password' => Hash::make('password'),
    ]);
    \Pest\Laravel\postJson('/api/v1/auth/forgot-password', [
        'email' => $email,
    ])->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);
    Notification::assertSentTo($user, \App\Notifications\ResetPasswordNotification::class);
    Notification::assertCount(1);
});
it('can reset password from password reset email', function () {
    Notification::fake();
    $email = fake()->safeEmail();
    $newPassword = 'Newpassword!@1!';
    $user = \App\Models\User::factory()->create([
        'email' => $email,
        'password' => Hash::make('password'),
    ]);
    \Pest\Laravel\postJson('/api/v1/auth/forgot-password', [
        'email' => $email,
    ])->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);
    Notification::assertSentTo($user, \App\Notifications\ResetPasswordNotification::class);
    Notification::assertCount(1);

    $token = Password::broker()->createToken($user);

    \Pest\Laravel\postJson('/api/v1/auth/reset-password', [
        'token' => $token,
        'email' => $email,
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ])->assertStatus(200)
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
