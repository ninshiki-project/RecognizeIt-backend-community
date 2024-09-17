<?php

use App\Models\Posts;

it('can like a post', function () {
    \App\Models\Posts::factory()->count(5)->create();
    $id = \App\Models\Posts::all()->random(1)->value('id');
    \Pest\Laravel\patchJson('/api/v1/posts/'.$id.'/toggle/like')
        ->assertStatus(200);
    $this->assertDatabaseCount(config('like.likes_table'), 1);
});

it('can create a new post', function () {
    $count = Posts::count();
    \Pest\Laravel\postJson('/api/v1/posts', [
        'post_content' => fake()->paragraph(10),
        'amount' => 3,
        'attachment_type' => 'gif',
        'gif_url' => fake()->imageUrl(word: 'ninshiki-testing'),
        'type' => 'user',
        'recipient_id' => \App\Models\User::all()->random(3)->pluck('id')->toArray(),
    ])
        ->assertStatus(201)
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseCount('posts', $count + 1);
});

it('can get all posts', function () {
    $dbCount = Posts::count();
    $count = 5;
    \App\Models\Posts::factory()->count($count)->create();
    \Pest\Laravel\getJson('/api/v1/posts')
        ->assertStatus(200);
    $this->assertDatabaseCount('posts', $dbCount + $count);
});
it('can get all 5 post', function () {
    $dbCount = Posts::count();
    $count = 20;
    $total = $count + $dbCount;
    \App\Models\Posts::factory()->count($count)->create();
    \Pest\Laravel\getJson('/api/v1/posts?per_page=5')
        ->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('meta.total', $total)
        ->assertJsonPath('meta.per_page', 5);
    $this->assertDatabaseCount('posts', $total);
});

it('can like/unlike a post', function () {
    $post = \App\Models\Posts::inRandomOrder()->first();
    \Pest\Laravel\patchJson('/api/v1/posts/'.$post->id.'/toggle/like')
        ->assertStatus(200);
});

it('can set to any page of the pagination in post', function () {
    $dbCount = Posts::count();
    $count = 20;
    \App\Models\Posts::factory()->count($count)->create();
    \Pest\Laravel\getJson('/api/v1/posts?per_page=5&page=2')
        ->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('meta.current_page', 2)
        ->assertJsonPath('meta.per_page', 5);
    $this->assertDatabaseCount('posts', $count + $dbCount);
});
