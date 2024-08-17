<?php

it('can like a post', function () {
    \App\Models\Posts::factory()->count(5)->create();
    $id = \App\Models\Posts::all()->random(1)->value('id');
    \Pest\Laravel\patchJson('/api/v1/posts/'.$id.'/like')
        ->assertStatus(200);
    $this->assertDatabaseCount('post_likes', 1);
});

it('can create a new post', function () {
    $resp = \Pest\Laravel\postJson('/api/v1/posts', [
        'post_content' => fake()->paragraph(10),
        'points' => 3,
        'attachment_type' => 'gif',
        'gif_url' => fake()->imageUrl(word: 'ninshiki-testing'),
        'type' => 'user',
        'recipient_id' => \App\Models\User::all()->random(3)->pluck('id')->toArray(),
    ])
        ->assertStatus(201)
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseCount('posts', 1);
});

it('can get all posts', function () {
    $count = 5;
    \App\Models\Posts::factory()->count($count)->create();
    \Pest\Laravel\getJson('/api/v1/posts')
        ->assertStatus(200);
    $this->assertDatabaseCount('posts', $count);
});
it('can get all 5 post', function () {
    $count = 20;
    \App\Models\Posts::factory()->count($count)->create();
    \Pest\Laravel\getJson('/api/v1/posts?perPage=5')
        ->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('meta.total', $count)
        ->assertJsonPath('meta.per_page', 5);
    $this->assertDatabaseCount('posts', $count);
});

it('can set to any page of the pagination in post', function () {
    $count = 20;
    \App\Models\Posts::factory()->count($count)->create();
    \Pest\Laravel\getJson('/api/v1/posts?perPage=5&page=2')
        ->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('meta.current_page', 2)
        ->assertJsonPath('meta.per_page', 5);
    $this->assertDatabaseCount('posts', $count);
});
