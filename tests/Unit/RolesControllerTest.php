<?php
it('can get all the roles', function () {
    \Pest\Laravel\getJson('/api/v1/roles')
        ->assertStatus(200);
});
it('show', function () {
    $role = \App\Models\Role::all()->random(1)->value('id');
    \Pest\Laravel\getJson('/api/v1/roles/'.$role)
        ->assertStatus(200);
});
