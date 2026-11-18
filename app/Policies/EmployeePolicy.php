<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use App\Enums\Role;

class EmployeePolicy
{
    private function isAdmin(User $user): bool
    {
        return $user->role === Role::Admin;
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, Employee $employee): bool
    {
        return $this->isAdmin($user)
            || $employee->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Employee $employee): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, Employee $employee): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, Employee $employee): bool
    {
        return $this->isAdmin($user);
    }
}
