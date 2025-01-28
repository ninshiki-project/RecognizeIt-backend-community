<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property string $id
 * @property string $designations_id
 * @property int $limit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Designations|null $designations
 * @property-read \App\Models\TFactory|null $use_factory
 * @method static \Database\Factories\PostingLimitFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostingLimit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostingLimit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostingLimit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostingLimit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostingLimit whereDesignationsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostingLimit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostingLimit whereLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostingLimit whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PostingLimit extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'designations_id',
        'limit',
    ];

    public function designations(): BelongsTo
    {
        return $this->belongsTo(Designations::class);
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
