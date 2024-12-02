<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: Posts.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Models;

use App\Observers\PostsObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Overtrue\LaravelLike\Traits\Likeable;

#[ObservedBy([PostsObserver::class])]
class Posts extends Model
{
    use HasFactory, Likeable;

    protected $fillable = [
        'content',
        'image',
        'type',
        'attachment_type',
        'attachment_url',
        'posted_by',
    ];

    public function recipients(): MorphMany
    {
        return $this->morphMany(Recipients::class, 'recipientable');
    }

    public function originalPoster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
