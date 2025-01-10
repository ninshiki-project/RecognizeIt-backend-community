<?php

namespace Tests\Http\Controllers\Api;

use App\Http\Controllers\Api\Enum\RedeemStatusEnum;
use App\Models\Redeem;
use App\Models\Shop;
use App\Models\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

it('can display specific redeem record', function () {
    getJson('/api/v1/redeems/'.Redeem::inRandomOrder()->first()->id)
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
    $user = User::first();
    $wallet = $user->getWallet('ninshiki-wallet');
    $wallet->deposit(400000);
    $shop = Shop::inRandomOrder()->first();
    $reps = postJson('/api/v1/redeems/shop', [
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
    getJson('/api/v1/redeems')
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
    getJson('/api/v1/redeems?status='.\Pest\Faker\fake()->randomElement(RedeemStatusEnum::cases())->value)
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
    $redeem = Redeem::where('status', '!=', RedeemStatusEnum::DECLINED)->where('status', '!=', RedeemStatusEnum::REDEEMED)->first();

    patchJson('/api/v1/redeems/'.$redeem->id, [
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
    $user = User::first();
    $wallet = $user->getWallet('ninshiki-wallet');
    $wallet->deposit(400000);
    $shop = Shop::inRandomOrder()->first();
    $reps = postJson('/api/v1/redeems/shop', [
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
    $redeem = Redeem::where('status', '=', RedeemStatusEnum::WAITING_APPROVAL)->first();
    deleteJson('/api/v1/redeems/'.$redeem->id)
        ->assertOk()
        ->assertJsonStructure([
            'message',
            'success',
        ]);
});
