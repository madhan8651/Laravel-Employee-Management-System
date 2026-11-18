<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\AdminEmployeeActivityNotification;

class AdminNotificationService
{
    public function notifyAdmins(
        Employee $employee,
        string $action,
        array $changedFields = [],
        ?int $performedBy = null
    ): void {
        $performedByName = null;

        if ($performedBy !== null) {
            $actor = User::find($performedBy);

            $performedByName = $actor?->email;
        }

        User::query()
            ->where('role', Role::Admin)
            ->get()
            ->each(function (User $admin) use (
                $employee,
                $action,
                $changedFields,
                $performedByName
            ) {
                $admin->notify(
                    new AdminEmployeeActivityNotification(
                        $employee,
                        $action,
                        $changedFields,
                        $performedByName
                    )
                );
            });
    }
}
