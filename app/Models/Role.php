<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: Role.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

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
