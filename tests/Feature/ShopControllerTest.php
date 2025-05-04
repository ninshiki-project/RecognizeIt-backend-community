<?php

namespace Tests\Http\Controllers\Api;

use App\Models\Products;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('can get all the product that are in the shop', function () {
    $user = User::factory()->create();
    \Pest\Laravel\actingAs($user)->getJson('/api/v1/shop')->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => [
            $json->has('data'),
        ]);
});
it('can add a product to the shop', function () {
    // get the product that doesn't exist in the shop
    $product = Products::factory()->create();
    $user = User::factory()->create();
    \Pest\Laravel\actingAs($user)->postJson('/api/v1/shop', [
        'product' => $product->id,
    ])
        ->assertStatus(201)
        ->assertJson(fn (AssertableJson $json) => [
            $json->has('data')
                ->has('data.product'),
        ]);
});

it('can add a shop to user wishlist', function () {
    $user = User::factory()->create();
    $data = \Pest\Laravel\actingAs($user)->postJson('/api/v1/shop/wishlist', [
        'shop' => Shop::inRandomOrder()->first()->id,
    ])
        ->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => [
            $json->has('message')
                ->has('success'),
        ]);
});

it('can remove the product in the shop', function () {
    $user = User::factory()->create();
    \Pest\Laravel\actingAs($user)->deleteJson('/api/v1/shop/'.Products::inRandomOrder()->first()->id)
        ->assertStatus(204);
});
