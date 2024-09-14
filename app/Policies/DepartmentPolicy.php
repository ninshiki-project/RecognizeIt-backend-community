<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: DepartmentPolicy.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Policies;

use App\Models\Departments;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view department');
    }

    public function view(User $user, Departments $departments): bool
    {
        return $user->can('view department');
    }

    public function create(User $user): bool
    {
        return $user->can('create department');
    }

    public function update(User $user, Departments $departments): bool
    {
        return $user->can('update department');
    }

    public function delete(User $user, Departments $departments): bool
    {
        return $user->can('delete department');
    }

    public function restore(User $user, Departments $departments): bool
    {
        return $user->can('restore department');
    }

    public function forceDelete(User $user, Departments $departments): bool
    {
        return $user->can('force delete department');
    }
}
