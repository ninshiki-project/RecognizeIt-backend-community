<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostLike extends Model
{
    protected $fillable = [
        'posts_id',
        'user_id',
    ];

    public function posts(): BelongsTo
    {
        return $this->belongsTo(Posts::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
