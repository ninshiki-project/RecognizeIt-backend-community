<?php

it('can get the points of authenticated user', function () {
    $reps = \Pest\Laravel\getJson('/api/v1/points');
    $reps->assertStatus(200)
        ->assertJson([
            'points_earned' => 0,
            'credits' => 30,
        ]);
});
