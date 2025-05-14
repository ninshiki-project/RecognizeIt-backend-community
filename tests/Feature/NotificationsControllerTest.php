<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: NotificationsControllerTest.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

it('can get all notifications for a user', function () {
    // Fake notifications to prevent actual sending
    Notification::fake();

    // Create a user
    $user = User::factory()->create();

    // Create notifications for the user
    $notificationCount = 5;
    for ($i = 0; $i < $notificationCount; $i++) {
        $notification = new DatabaseNotification([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\MentionNotification',
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'data' => [
                'message' => 'Test notification '.($i + 1),
                'postId' => $i + 1,
                'type' => 'mention',
            ],
            'read_at' => null,
        ]);

        $notification->save();
    }

    // Test the endpoint
    \Pest\Laravel\actingAs($user)
        ->getJson('/api/v1/notifications')
        ->assertStatus(200)
        ->assertJsonCount($notificationCount, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'notifiable_id',
                    'notifiable_type',
                    'data',
                    'read_at',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
});

it('can get unread notifications for a user', function () {
    // Fake notifications to prevent actual sending
    Notification::fake();

    // Create a user
    $user = User::factory()->create();

    // Create unread notifications
    $unreadCount = 3;
    for ($i = 0; $i < $unreadCount; $i++) {
        $notification = new DatabaseNotification([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\MentionNotification',
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'data' => [
                'message' => 'Unread notification '.($i + 1),
                'postId' => $i + 1,
                'type' => 'mention',
            ],
            'read_at' => null,
        ]);

        $notification->save();
    }

    // Create read notifications
    $readCount = 2;
    for ($i = 0; $i < $readCount; $i++) {
        $notification = new DatabaseNotification([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\MentionNotification',
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'data' => [
                'message' => 'Read notification '.($i + 1),
                'postId' => $i + 1,
                'type' => 'mention',
            ],
            'read_at' => now(),
        ]);

        $notification->save();
    }

    // Test the endpoint for unread notifications
    \Pest\Laravel\actingAs($user)
        ->getJson('/api/v1/notifications?unread=1')
        ->assertStatus(200)
        ->assertJsonCount($unreadCount, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'notifiable_id',
                    'notifiable_type',
                    'data',
                    'read_at',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
});

it('can get read notifications for a user', function () {
    // Fake notifications to prevent actual sending
    Notification::fake();

    // Create a user
    $user = User::factory()->create();

    // Create unread notifications
    $unreadCount = 2;
    for ($i = 0; $i < $unreadCount; $i++) {
        $notification = new DatabaseNotification([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\MentionNotification',
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'data' => [
                'message' => 'Unread notification '.($i + 1),
                'postId' => $i + 1,
                'type' => 'mention',
            ],
            'read_at' => null,
        ]);

        $notification->save();
    }

    // Create read notifications
    $readCount = 3;
    for ($i = 0; $i < $readCount; $i++) {
        $notification = new DatabaseNotification([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\MentionNotification',
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'data' => [
                'message' => 'Read notification '.($i + 1),
                'postId' => $i + 1,
                'type' => 'mention',
            ],
            'read_at' => now(),
        ]);

        $notification->save();
    }

    // Test the endpoint for read notifications
    \Pest\Laravel\actingAs($user)
        ->getJson('/api/v1/notifications?read=1')
        ->assertStatus(200)
        ->assertJsonCount($readCount, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'notifiable_id',
                    'notifiable_type',
                    'data',
                    'read_at',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
});

it('can mark a notification as read', function () {
    // Fake notifications to prevent actual sending
    Notification::fake();

    // Create a user
    $user = User::factory()->create();

    // Create an unread notification
    $notification = new DatabaseNotification([
        'id' => Str::uuid()->toString(),
        'type' => 'App\\Notifications\\MentionNotification',
        'notifiable_type' => get_class($user),
        'notifiable_id' => $user->id,
        'data' => [
            'message' => 'Test notification to mark as read',
            'postId' => 1,
            'type' => 'mention',
        ],
        'read_at' => null,
    ]);

    $notification->save();

    // Test marking the notification as read
    \Pest\Laravel\actingAs($user)
        ->patchJson('/api/v1/notifications/'.$notification->id.'/read')
        ->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
});

it('returns 404 when trying to mark a non-existent notification as read', function () {
    // Create a user
    $user = User::factory()->create();

    // Test with a non-existent notification ID
    $nonExistentId = Str::uuid()->toString();

    \Pest\Laravel\actingAs($user)
        ->patchJson('/api/v1/notifications/'.$nonExistentId.'/read')
        ->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Notification record not found',
        ]);
});
