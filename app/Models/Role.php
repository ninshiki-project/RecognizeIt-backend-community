<?php

namespace App\Models;

use Spatie\Permission\Models\Role as BaseRoleModel;

class Role extends BaseRoleModel
{
    protected $hidden = [
        'pivot',
        'created_at',
        'updated_at',
        'guard_name',
    ];
}
