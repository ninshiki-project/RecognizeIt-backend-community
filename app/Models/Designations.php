<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property int $departments_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Departments|null $departments
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \App\Models\PostingLimit|null $postingLimits
 * @method static \Database\Factories\DesignationsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Designations newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Designations newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Designations query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Designations whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Designations whereDepartmentsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Designations whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Designations whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Designations whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
