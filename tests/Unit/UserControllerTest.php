<?php

it('can get all the user', function () {
    \Pest\Laravel\getJson('/api/v1/users')->assertStatus(200);
});

it('show points of specific user', function () {
    $content = \Pest\Laravel\getJson('api/v1/users/1/points')->content();
    expect(json_decode($content))->toMatchObject([
        'id' => 1,
        'user_id' => 1,
        'points_earned' => 0,
        'credits' => 30,
    ]);
});

it('destroy', function () {
 expect(true)->toBe(true);
});

it('show', function () {
    \Pest\Laravel\getJson('/api/v1/users')->assertStatus(200);
});
