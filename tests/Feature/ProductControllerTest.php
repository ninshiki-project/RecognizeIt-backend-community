<?php

namespace Tests\Http\Controllers\Api;

use App\Http\Controllers\Api\Enum\ProductStatusEnum;
use App\Models\Products;
use App\Models\User;
use Illuminate\Http\UploadedFile;

it('can get all the product', function () {

    $user = User::factory()->create();
    \Pest\Laravel\actingAs($user)->getJson('/api/v1/products')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'stock',
                    'status',
                    'price',
                ],
            ],
        ]);
});
it('can create a new product', function () {

    $user = User::factory()->create();

    $file = UploadedFile::fake()->image('avatar.jpg');

    $resp = \Pest\Laravel\actingAs($user)->postJson('/api/v1/products', [
        'name' => 'Test Product'.random_int(1, 100),
        'description' => \Pest\Faker\fake()->text(),
        'price' => random_int(10, 100),
        'stock' => random_int(10, 100),
        'image' => $file,
    ])->assertStatus(201);
});
it('can update the product with batch fields', function () {

    $file = UploadedFile::fake()->image('avatar.jpg');
    $user = User::factory()->create();

    \Pest\Laravel\actingAs($user)->putJson('/api/v1/products/'.Products::inRandomOrder()->first()->id, [
        'name' => 'Fake Product'.random_int(1, 100),
        'description' => \Pest\Faker\fake()->text(),
        'price' => random_int(10, 100),
        'stock' => random_int(10, 100),
        'image' => $file,
        'status' => \Pest\Faker\fake()->randomElement(ProductStatusEnum::cases())->value,
    ])
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'stock',
                'status',
            ],
        ]);
});
it('can update the product description only', function () {

    $user = User::factory()->create();

    \Pest\Laravel\actingAs($user)->putJson('/api/v1/products/'.Products::inRandomOrder()->first()->id, [
        'description' => \Pest\Faker\fake()->text(),
    ])
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'stock',
                'status',
            ],
        ]);
});
it('can update the product stock only', function () {

    $user = User::factory()->create();

    \Pest\Laravel\actingAs($user)->putJson('/api/v1/products/'.Products::inRandomOrder()->first()->id, [
        'stock' => random_int(1, 100),
    ])
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'stock',
                'status',
            ],
        ]);
});
it('can update the product status only', function () {

    $user = User::factory()->create();

    $resp = \Pest\Laravel\actingAs($user)->putJson('/api/v1/products/'.Products::inRandomOrder()->first()->id, [
        'status' => \Pest\Faker\fake()->randomElement(ProductStatusEnum::cases())->value,

    ])
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'stock',
                'status',
            ],
        ]);
});
it('can update the product image only', function () {

    $file = UploadedFile::fake()->image('avatar.jpg');
    $user = User::factory()->create();

    \Pest\Laravel\actingAs($user)->putJson('/api/v1/products/'.Products::inRandomOrder()->first()->id, [
        'image' => $file,
    ])
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'stock',
                'status',
            ],
        ]);
});
it('can update the product name only', function () {

    $user = User::factory()->create();

    \Pest\Laravel\actingAs($user)->putJson('/api/v1/products/'.Products::inRandomOrder()->first()->id, [
        'name' => 'Fake Product'.random_int(50, 100),
    ])
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'stock',
                'status',
            ],
        ]);
});
it('it get the specific information of the product', function () {
    $user = User::factory()->create();

    \Pest\Laravel\actingAs($user)->getJson('/api/v1/products/'.Products::inRandomOrder()->first()->id)
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'stock',
                'status',
            ],
        ]);
});
it('can delete the product that is not in use', function () {
    Products::all()->each(function ($product) {
        if (! $product->shop()->exists() && ! $product->redeems()->exists()) {
            $user = User::factory()->create();

            \Pest\Laravel\actingAs($user)->deleteJson('/api/v1/products/'.$product->id)
                ->assertStatus(204)
                ->assertNoContent();
        }
    });
    expect(true)->toBeTrue();
});
