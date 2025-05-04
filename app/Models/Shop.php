<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: Shop.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Overtrue\LaravelFavorite\Traits\Favoriteable;

class Shop extends Model
{
    use Favoriteable, HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
    ];

    protected $casts = [
        'id' => 'string',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'product_id', 'id');
    }

    public function redeems(): HasMany
    {
        return $this->hasMany(Redeem::class, 'shop_id', 'id');
    }

    protected static function booted(): void
    {
        static::deleted(function (Shop $shop) {
            // delete the shop record in the user favorite table
            $shop->favorites()->each(fn ($favorite) => $favorite->delete());
        });
    }
}
