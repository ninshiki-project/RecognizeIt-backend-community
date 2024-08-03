<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'department',
        'invited_by_user',
        'token',
        'role',
        'accepted_at',
        'status',
    ];

    protected $hidden = [
        'token',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user');
    }
}
