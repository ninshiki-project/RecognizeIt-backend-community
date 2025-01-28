<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: Redeem.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Models;

use App\Http\Controllers\Api\Enum\RedeemStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property string $id
 * @property string $shop_id
 * @property string $product_id
 * @property int $user_id
 * @property RedeemStatusEnum $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $decline_reason_category
 * @property string|null $decline_reason
 * @property string|null $declined_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \App\Models\Products|null $product
 * @property-read \App\Models\Shop|null $shop
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\RedeemFactory factory($count = null, $state = [])
 * @method static Builder<static>|Redeem newModelQuery()
 * @method static Builder<static>|Redeem newQuery()
 * @method static Builder<static>|Redeem query()
 * @method static Builder<static>|Redeem status(?string $status)
 * @method static Builder<static>|Redeem user(array|string|null $userId)
 * @method static Builder<static>|Redeem whereCreatedAt($value)
 * @method static Builder<static>|Redeem whereDeclineReason($value)
 * @method static Builder<static>|Redeem whereDeclineReasonCategory($value)
 * @method static Builder<static>|Redeem whereDeclinedAt($value)
 * @method static Builder<static>|Redeem whereId($value)
 * @method static Builder<static>|Redeem whereProductId($value)
 * @method static Builder<static>|Redeem whereShopId($value)
 * @method static Builder<static>|Redeem whereStatus($value)
 * @method static Builder<static>|Redeem whereUpdatedAt($value)
 * @method static Builder<static>|Redeem whereUserId($value)
 * @mixin \Eloquent
 */
class Redeem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'status',
        'user_id',
        'shop_id',
        'decline_reason_category',
        'decline_reason',
        'declined_at',
    ];

    protected $casts = [
        'status' => RedeemStatusEnum::class,
    ];

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'product_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }

    /**
     * @param  Builder  $query
     * @param  string|array|null  $userId
     * @return Builder
     */
    public function scopeUser(Builder $query, string|array|null $userId): Builder
    {
        if (! $userId) {
            return $query;
        }

        if (is_array($userId)) {
            return $query->whereIn('user_id', $userId);
        }

        return $query->where('user_id', $userId);
    }

    /**
     * @param  Builder  $query
     * @param  string|null  $status
     * @return Builder
     */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (! $status) {
            return $query;
        }

        return $query->where('status', $status);
    }
}
