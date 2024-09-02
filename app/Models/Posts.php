<?php

namespace App\Models;

use App\Observers\PostsObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
