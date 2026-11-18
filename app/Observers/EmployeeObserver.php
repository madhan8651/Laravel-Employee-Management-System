<?php

namespace App\Observers;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendBirthdayWishJob;

class EmployeeObserver
{
    /**
     * Log employee creation.
     */
    public function created(Employee $employee): void
    {
        $employee->activityLogs()->create([
            'action' => 'created',
            'description' => "Employee created: {$employee->name}",
            'user_id' => Auth::id(),
            'new_values' => $employee->getAttributes(),
        ]);
    }

    /**
     * Log employee updates.
     */
    public function updated(Employee $employee): void
    {
        $changes = $employee->getChanges();

        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $oldValues = [];

        foreach (array_keys($changes) as $key) {
            $oldValues[$key] = $employee->getRawOriginal($key);
        }

        $employee->activityLogs()->create([
            'action' => 'updated',
            'description' => "Employee updated: {$employee->name}",
            'user_id' => Auth::id(),
            'old_values' => $oldValues,
            'new_values' => $changes,
        ]);

        // Queue birthday email when DOB changes.
        if ($employee->wasChanged('date_of_birth')) {
            SendBirthdayWishJob::dispatch($employee->id);
        }
    }

    /**
     * Log employee deletion or deactivation.
     */
    public function deleted(Employee $employee): void
    {
        $employee->activityLogs()->create([
            'action' => 'deleted',
            'description' => "Employee deleted or deactivated: {$employee->name}",
            'user_id' => Auth::id(),
            'old_values' => $employee->getAttributes(),
        ]);
    }

    /**
     * Log employee restoration.
     */
    public function restored(Employee $employee): void
    {
        $employee->activityLogs()->create([
            'action' => 'restored',
            'description' => "Employee restored: {$employee->name}",
            'user_id' => Auth::id(),
            'new_values' => $employee->getAttributes(),
        ]);
    }

    /**
     * Log permanent deletion.
     */
    public function forceDeleted(Employee $employee): void
    {
        $employee->activityLogs()->create([
            'action' => 'force_deleted',
            'description' => "Employee permanently deleted: {$employee->name}",
            'user_id' => Auth::id(),
            'old_values' => $employee->getAttributes(),
        ]);
    }
}
