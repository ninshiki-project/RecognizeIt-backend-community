<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: Recipients.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $recipientable_type
 * @property int $recipientable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read Model|\Eloquent $recipientable
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\RecipientsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipients newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipients newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipients query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipients whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipients whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipients whereRecipientableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipients whereRecipientableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipients whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Recipients whereUserId($value)
 * @mixin \Eloquent
 */
class Recipients extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'receivable_id',
        'receivable_type',
    ];

    public function recipientable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
