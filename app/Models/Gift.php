<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: Gift.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Models;

use App\Enum\GiftEnum;
use Awobaz\Compoships\Compoships;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gift extends Model
{
    use Compoships, HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'by',
        'to',
        'type',
        'gift',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
            'gift' => 'array',
            'type' => GiftEnum::class,
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, ['by', 'to'], ['id', 'id']);
    }

    public static function sentGiftInAMonth(User $user): int
    {
        return self::where('by', $user->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
    }

    public static function sentGiftInAWeek(User $user): int
    {
        return self::where('by', $user->id)
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(Carbon::MONDAY),
                Carbon::now()->endOfWeek(Carbon::SUNDAY),
            ])
            ->count();
    }

    public static function sentGiftInAYear(User $user): int
    {
        return self::where('by', $user->id)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
    }
}
