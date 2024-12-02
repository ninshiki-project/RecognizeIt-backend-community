<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: UsersPolicy.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UsersPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view user');
    }

    public function view(User $user, User $model): bool
    {
        if ($user->can('view user')) {
            return true;
        }

        return $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->can('invite user');
    }

    public function update(User $user, User $model): bool
    {
        if ($user->can('update user')) {
            return true;
        }

        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->can('delete user')) {
            return true;
        }

        return $user->id === $model->id;
    }

    public function restore(User $user, User $model): bool
    {
        return $user->can('restore user');
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->can('force delete user');
    }
}
