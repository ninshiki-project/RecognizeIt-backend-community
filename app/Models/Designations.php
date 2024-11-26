<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
