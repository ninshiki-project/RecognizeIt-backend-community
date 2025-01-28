<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: Departments.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $department_head
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read \App\Models\User|null $head
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\DepartmentsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments whereDepartmentHead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departments withoutTrashed()
 * @mixin \Eloquent
 */
class Departments extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'department_head',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department');
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'department_head');
    }
}
