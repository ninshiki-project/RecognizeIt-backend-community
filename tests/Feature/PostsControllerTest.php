<?php

use App\Models\Posts;
use App\Models\User;

it('can like a post', function () {
    Event::fake();
    Notification::fake();
    $user = User::factory()->create();
    \App\Models\Posts::factory()->count(5)->create();
    $id = \App\Models\Posts::all()->random(1)->value('id');
    \Pest\Laravel\actingAs($user)->patchJson('/api/v1/posts/'.$id.'/toggle/like')
        ->assertStatus(200);
    $this->assertDatabaseCount(config('like.likes_table'), 1);
});

it('can create a new post', function () {
    Event::fake();
    Notification::fake();
    $user = User::factory()->create();
    $spendWallet = $user->createWallet([
        'name' => 'Spend Wallet',
        'slug' => 'spend-wallet',
        'meta' => [
            'currency' => 'NSW',
        ],
    ]);
    $spendWallet->deposit(500000);
    $count = Posts::count();
    $user1 = User::factory()->create();
    $user1->createWallet([
        'name' => 'Ninshiki Wallet',
        'slug' => 'ninshiki-wallet',
        'meta' => [
            'currency' => 'NSK',
        ],
    ]);
    $user2 = User::factory()->create();
    $user2->createWallet([
        'name' => 'Ninshiki Wallet',
        'slug' => 'ninshiki-wallet',
        'meta' => [
            'currency' => 'NSK',
        ],
    ]);
    $user3 = User::factory()->create();
    $user3->createWallet([
        'name' => 'Ninshiki Wallet',
        'slug' => 'ninshiki-wallet',
        'meta' => [
            'currency' => 'NSK',
        ],
    ]);
    $resp = \Pest\Laravel\actingAs($user)->postJson('/api/v1/posts', [
        'post_content' => fake()->paragraph(10),
        'amount' => 5,
        'attachment_type' => 'gif',
        'gif_url' => fake()->imageUrl(word: 'ninshiki-testing'),
        'type' => 'user',
        'recipient_id' => [$user1->id, $user2->id, $user3->id],
    ])
        ->assertStatus(201)
        ->assertJson([
            'success' => true,
        ]);

    //    $this->assertDatabaseCount('posts', $count + 1);
});

it('can get all posts', function () {
    Event::fake();
    Notification::fake();
    $user = User::factory()->create();
    $count = Posts::count();
    $dbCount = Posts::count();
    $count = 5;
    \App\Models\Posts::factory()->count($count)->create();
    \Pest\Laravel\actingAs($user)->getJson('/api/v1/posts')
        ->assertStatus(200);
    $this->assertDatabaseCount('posts', $dbCount + $count);
});
it('can get all 5 post', function () {
    Event::fake();
    Notification::fake();
    $user = User::factory()->create();
    $dbCount = Posts::count();
    $count = 20;
    $total = $count + $dbCount;
    \App\Models\Posts::factory()->count($count)->create();
    \Pest\Laravel\actingAs($user)->getJson('/api/v1/posts?per_page=5')
        ->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('meta.total', $total)
        ->assertJsonPath('meta.per_page', 5);
    $this->assertDatabaseCount('posts', $total);
});

it('can like/unlike a post', function () {
    Event::fake();
    Notification::fake();
    $user = User::factory()->create();
    $post = \App\Models\Posts::inRandomOrder()->first();
    \Pest\Laravel\actingAs($user)->patchJson('/api/v1/posts/'.$post->id.'/toggle/like')
        ->assertStatus(200);
});

it('can set to any page of the pagination in post', function () {
    Event::fake();
    Notification::fake();
    $user = User::factory()->create();
    $dbCount = Posts::count();
    $count = 20;
    \App\Models\Posts::factory()->count($count)->create();
    \Pest\Laravel\actingAs($user)->getJson('/api/v1/posts?per_page=5&page=2')
        ->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('meta.current_page', 2)
        ->assertJsonPath('meta.per_page', 5);
    $this->assertDatabaseCount('posts', $count + $dbCount);
});
