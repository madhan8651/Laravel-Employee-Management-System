<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use App\Enums\Role;

class DepartmentPolicy
{
    private function isAdmin(User $user): bool
    {
        return $user->role === Role::Admin;
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, Department $department): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Department $department): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Department $department): bool
    {
        return $this->isAdmin($user);
    }
}
