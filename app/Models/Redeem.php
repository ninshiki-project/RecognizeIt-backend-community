<?php

namespace App\Models;

use App\Http\Controllers\Api\Enum\RedeemStatusEnum;
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
    ];

    protected $casts = [
        'status' => RedeemStatusEnum::class,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'product_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }

    public function scopeUser($query, $userId)
    {
        if (! $userId) {
            return $query;
        }
        if (is_array($userId)) {
            return $query->whereIn('user_id', $userId);
        }

        return $query->where('user_id', $userId);
    }

    public function scopeStatus($query, $status)
    {
        if (! $status) {
            return $query;
        }

        return $query->where('status', $status);
    }
}
