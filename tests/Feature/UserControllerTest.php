<?php

it('can get all the user', function () {
    \Pest\Laravel\getJson('/api/v1/users')->assertStatus(200);
});

it('can show all user', function () {
    \Pest\Laravel\getJson('/api/v1/users')->assertStatus(200);
});
