<?php

namespace Tests\Http\Controllers\Api;

use App\Http\Controllers\Api\Enum\RedeemStatusEnum;
use App\Http\Controllers\Api\Enum\WalletsEnum;
use App\Models\Redeem;
use App\Models\Shop;
use App\Models\User;

use function Pest\Laravel\deleteJson;

it('can display specific redeem record', function () {
    $user = User::factory()->create();
    \Pest\Laravel\actingAs($user)->getJson('/api/v1/redeems/'.Redeem::inRandomOrder()->first()->id)
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'user',
                'product',
                'status',
            ],
        ]);
});
it('can redeem from the shop', function () {
    $user = User::factory()->create();
    $user->getWallet(WalletsEnum::DEFAULT->value)->deposit(60000000);
    $shop = Shop::inRandomOrder()->first();
    $reps = \Pest\Laravel\actingAs($user)->postJson('/api/v1/redeems/shop', [
        'shop' => $shop?->id,
    ])
        ->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'user',
                'product',
                'status',
            ],
        ]);
});
it('can display all the redeem item', function () {
    $user = User::factory()->create();
    \Pest\Laravel\actingAs($user)->getJson('/api/v1/redeems')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user' => [
                        'id',
                        'name',
                    ],
                    'product' => [
                        'id',
                        'name',
                    ],
                    'status',
                ],
            ],
        ]);
});
it('can display all the redeem item by status', function () {
    $user = User::factory()->create();
    \Pest\Laravel\actingAs($user)->getJson('/api/v1/redeems?status='.\Pest\Faker\fake()->randomElement(RedeemStatusEnum::cases())->value)
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user' => [
                        'id',
                        'name',
                    ],
                    'product' => [
                        'id',
                        'name',
                    ],
                    'status',
                ],
            ],
        ]);
});
it('can update the status', function () {
    $redeem = Redeem::factory()->create([
        'status' => RedeemStatusEnum::WAITING_APPROVAL,
    ]);

    $user = User::factory()->create();
    $resp = \Pest\Laravel\actingAs($user)->patchJson('/api/v1/redeems/'.$redeem->id, [
        'status' => RedeemStatusEnum::REDEEMED->value,
    ])
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'user',
                'product',
                'status',
            ],
        ]);
});
it('can cancel the redeem item from shop if still in waiting for approval', function () {
    $user = User::factory()->create();
    $wallet = $user->getWallet('ninshiki-wallet');
    $wallet->deposit(400000);
    $shop = Shop::inRandomOrder()->first();

    $reps = \Pest\Laravel\actingAs($user)->postJson('/api/v1/redeems/shop', [
        'shop' => $shop?->id,
    ])
        ->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'user',
                'product',
                'status',
            ],
        ]);
    $data = $reps->json('data');
    deleteJson('/api/v1/redeems/'.$data['id'])
        ->assertOk()
        ->assertJsonStructure([
            'message',
            'success',
        ]);
});
