<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Posts extends Model
{
    use HasFactory;

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

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class, 'post_id');
    }
}
