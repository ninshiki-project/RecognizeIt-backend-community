<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: Products.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Models;

use App\Http\Controllers\Api\Enum\ProductStatusEnum;
use App\Models\Scopes\ProductAvailableScope;
use Bavix\Wallet\Interfaces\Customer;
use Bavix\Wallet\Interfaces\ProductInterface;
use Bavix\Wallet\Traits\HasWallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Wildside\Userstamps\Userstamps;

/**
 * 
 *
 * @property string $id
 * @property string $image
 * @property string|null $cloudinary_id
 * @property string $name
 * @property string|null $description
 * @property int $price
 * @property int $stock
 * @property ProductStatusEnum $status
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\User|null $destroyer
 * @property-read \App\Models\User|null $editor
 * @property-read non-empty-string $balance
 * @property-read int $balance_int
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \Bavix\Wallet\Models\Wallet $wallet
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Bavix\Wallet\Models\Transfer> $receivedTransfers
 * @property-read int|null $received_transfers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Redeem> $redeems
 * @property-read int|null $redeems_count
 * @property-read \App\Models\Shop|null $shop
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Bavix\Wallet\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Bavix\Wallet\Models\Transfer> $transfers
 * @property-read int|null $transfers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Bavix\Wallet\Models\Transaction> $walletTransactions
 * @property-read int|null $wallet_transactions_count
 * @method static Builder<static>|Products available()
 * @method static \Database\Factories\ProductsFactory factory($count = null, $state = [])
 * @method static Builder<static>|Products newModelQuery()
 * @method static Builder<static>|Products newQuery()
 * @method static Builder<static>|Products query()
 * @method static Builder<static>|Products unavailable()
 * @method static Builder<static>|Products whereCloudinaryId($value)
 * @method static Builder<static>|Products whereCreatedAt($value)
 * @method static Builder<static>|Products whereCreatedBy($value)
 * @method static Builder<static>|Products whereDeletedBy($value)
 * @method static Builder<static>|Products whereDescription($value)
 * @method static Builder<static>|Products whereId($value)
 * @method static Builder<static>|Products whereImage($value)
 * @method static Builder<static>|Products whereName($value)
 * @method static Builder<static>|Products wherePrice($value)
 * @method static Builder<static>|Products whereStatus($value)
 * @method static Builder<static>|Products whereStock($value)
 * @method static Builder<static>|Products whereUpdatedAt($value)
 * @method static Builder<static>|Products whereUpdatedBy($value)
 * @mixin \Eloquent
 */
class Products extends Model implements ProductInterface
{
    use HasFactory, HasUuids, HasWallet, Userstamps;

    protected $fillable = [
        'cloudinary_id',
        'name',
        'description',
        'price',
        'stock',
        'status',
        'image',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'stock' => 'integer',
        'status' => ProductStatusEnum::class,
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ProductAvailableScope);

        static::updated(function ($product) {
            if ($product->stock === 0 && $product->status === ProductStatusEnum::AVAILABLE) {
                $product->status = ProductStatusEnum::UNAVAILABLE;
                $product->save();
            }
        });
    }

    /**
     * @param  Builder  $query
     * @return mixed
     */
    public function scopeAvailable(Builder $query): mixed
    {
        return $query->where('status', ProductStatusEnum::AVAILABLE->value);
    }

    /**
     * @param  Builder  $query
     * @return mixed
     */
    public function scopeUnavailable(Builder $query): mixed
    {
        return $query->where('status', ProductStatusEnum::UNAVAILABLE->value);
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }

    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class, 'product_id', 'id');
    }

    public function redeems(): HasMany
    {
        return $this->hasMany(Redeem::class, 'product_id', 'id');
    }

    /**
     * @param  Customer  $customer
     * @return int|string
     */
    public function getAmountProduct(Customer $customer): int|string
    {
        return $this->price;
    }

    /**
     * @return array|mixed[]|null
     */
    public function getMetaProduct(): ?array
    {
        return [
            'title' => $this->name,
            'description' => 'Purchase of Product #'.$this->id,
        ];
    }
}
