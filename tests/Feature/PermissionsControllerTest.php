<?php

it('can get all the permissions', function () {
    $count = \App\Models\Permission::count();
    \Pest\Laravel\getJson('/api/v1/permissions')
        ->assertStatus(200);
    $this->assertDatabaseCount('permissions', $count);
});
it('can display a specific permission', function () {
    $count = \App\Models\Permission::count();
    $permission = \App\Models\Permission::all()->random(1)->value('id');
    \Pest\Laravel\getJson('/api/v1/permissions/'.$permission)
        ->assertStatus(200);
    $this->assertDatabaseCount('permissions', $count);
});
it('can get all the permission of the authenticated user', function () {
    $count = \App\Models\Permission::count();
    $resp = \Pest\Laravel\getJson('/api/v1/profile/permissions')
        ->assertStatus(200);

    $this->assertDatabaseCount('permissions', $count);
});
