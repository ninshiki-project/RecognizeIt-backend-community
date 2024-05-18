<?php

namespace App\Policies;

use App\Models\Departments;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return  $user->can('view department');
    }

    public function view(User $user, Departments $departments): bool
    {
        return  $user->can('view department');
    }

    public function create(User $user): bool
    {
        return  $user->can('create department');
    }

    public function update(User $user, Departments $departments): bool
    {
        return  $user->can('update department');
    }

    public function delete(User $user, Departments $departments): bool
    {
        return  $user->can('delete department');
    }

    public function restore(User $user, Departments $departments): bool
    {
        return  $user->can('restore department');
    }

    public function forceDelete(User $user, Departments $departments): bool
    {
        return  $user->can('force delete department');
    }
}
