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
/**
 * 
 *
 * @property int $id
 * @property string $content
 * @property string|null $attachment_type
 * @property string|null $cloudinary_id
 * @property string|null $attachment_url
 * @property string $type
 * @property int $posted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $likers
 * @property-read int|null $likers_count
 * @property-read \App\Models\User|null $originalPoster
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Recipients> $recipients
 * @property-read int|null $recipients_count
 * @property-read mixed $total_likers
 * @method static \Database\Factories\PostsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Posts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Posts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Posts query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Posts whereAttachmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Posts whereAttachmentUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Posts whereCloudinaryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Posts whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Posts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Posts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Posts wherePostedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Posts whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Posts whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Posts extends Model
{
    use HasFactory, Likeable;

    protected $fillable = [
        'content',
        'image',
        'type',
        'cloudinary_id',
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
