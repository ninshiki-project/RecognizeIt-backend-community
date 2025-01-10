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
        'decline_date',
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
