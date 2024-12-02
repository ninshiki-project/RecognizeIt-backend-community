<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Designations extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'departments_id',
    ];

    public function departments(): BelongsTo
    {
        return $this->belongsTo(Departments::class);
    }

    public function postingLimits(): HasOne
    {
        return $this->hasOne(PostingLimit::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
        ];
    }
}
