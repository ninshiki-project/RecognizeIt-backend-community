<?php

namespace Tests\Http\Controllers\Api;

use App\Models\Products;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('can get all the product that are in the shop', function () {
    getJson('/api/v1/shop')->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => [
            $json->has('data'),
        ]);
});
it('can add a product to the shop', function () {
    // get the product that doesn't exist in the shop
    $product = Products::has('shop', '<', 1)->first();
    postJson('/api/v1/shop', [
        'product' => $product->id,
    ])
        ->assertStatus(201)
        ->assertJson(fn (AssertableJson $json) => [
            $json->has('data')
                ->has('data.product'),
        ]);
});
it('can remove the product in the shop', function () {
    deleteJson('/api/v1/shop/'.Products::inRandomOrder()->first()->id)
        ->assertStatus(204);
});
