<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: Permission.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

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
