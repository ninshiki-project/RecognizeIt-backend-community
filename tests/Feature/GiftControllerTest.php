<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: GiftControllerTest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

use App\Models\User;

test('it can enable the gift feature via API', function () {
    $user = User::factory()->create();
    \Pest\Laravel\actingAs($user)->postJson('/api/v1/gifts/enable', [
        'enable' => true,
        'limit_count' => 5,
        'frequency' => 'monthly',
    ])->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

});
test('It can retrieve all the gift transactions in pagination format', function () {
    $user = User::factory()->create();
    \Pest\Laravel\actingAs($user)->getJson('/api/v1/gifts/')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);
});
test('it can retrieve gift meta', function () {
    $user = User::factory()->create();
    \Pest\Laravel\actingAs($user)->getJson('/api/v1/gifts/meta')
        ->assertStatus(200)
        ->assertJsonStructure([
            'gift_type',
            'exchange_rate',
        ]);
});

test('it cannot send a gift if the feature is disabled', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    \Pest\Laravel\actingAs($user1)->postJson('/api/v1/gifts/enable', [
        'enable' => false,
        'limit_count' => 5,
        'frequency' => 'monthly',
    ])->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $resp = \Pest\Laravel\actingAs($user1)->postJson('/api/v1/gifts/send', [
        'receiver' => $user2->id,
        'type' => 'coins',
        'amount' => 1,
    ])
        ->assertStatus(422)
        ->assertJsonIsObject()
        ->assertJsonStructure([
            '*' => [
                'message',
                'errors',
            ],
        ]);

});

test('user can send gift using coins', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    \Pest\Laravel\actingAs($user1)->postJson('/api/v1/gifts/enable', [
        'enable' => true,
        'limit_count' => 5,
        'frequency' => 'monthly',
    ])->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    \Pest\Laravel\actingAs($user1)->postJson('/api/v1/gifts/send', [
        'receiver' => $user2->id,
        'type' => 'coins',
        'amount' => 1,
    ])
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Gift has been sent successfully.',
        ]);
});
