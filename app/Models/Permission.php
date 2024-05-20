<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    protected $hidden = [
        'pivot',
        'created_at',
        'updated_at',
        'guard_name',
    ];
}
