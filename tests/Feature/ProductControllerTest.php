<?php

namespace Tests\Http\Controllers\Api;

use App\Models\Products;
use Illuminate\Http\UploadedFile;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('can get all the product', function () {

    getJson('/api/v1/products')
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

    $file = UploadedFile::fake()->image('avatar.jpg');

    postJson('/api/v1/products', [
        'name' => 'Test Product',
        'description' => 'Test Product',
        'price' => rand(10, 100),
        'stock' => rand(10, 100),
        'image' => $file,
    ]);
});
it('can update the product with batch fields', function () {

    $file = UploadedFile::fake()->image('avatar.jpg');

    putJson('/api/v1/products/'.Products::inRandomOrder()->first()->id, [
        'name' => 'Fake Product'.random_int(1, 100),
        'description' => \Pest\Faker\fake()->text(),
        'price' => random_int(1, 100),
        'stock' => random_int(1, 100),
        'image' => $file,
        'status' => collect(['unavailable', 'available'])->random(1)[0],
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

    $file = UploadedFile::fake()->image('avatar.jpg');

    putJson('/api/v1/products/'.Products::inRandomOrder()->first()->id, [
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

    $file = UploadedFile::fake()->image('avatar.jpg');

    putJson('/api/v1/products/'.Products::inRandomOrder()->first()->id, [
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

    $file = UploadedFile::fake()->image('avatar.jpg');

    $resp = putJson('/api/v1/products/'.Products::inRandomOrder()->first()->id, [
        'status' => collect(['available', 'unavailable'])->random(1)[0],
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

    putJson('/api/v1/products/'.Products::inRandomOrder()->first()->id, [
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

    $file = UploadedFile::fake()->image('avatar.jpg');

    putJson('/api/v1/products/'.Products::inRandomOrder()->first()->id, [
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
    getJson('/api/v1/products/'.Products::inRandomOrder()->first()->id)
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
it('can delete the product', function () {
    $resp = deleteJson('/api/v1/products/'.Products::inRandomOrder()->first()->id)
        ->assertStatus(200)
        ->assertNoContent();

});
