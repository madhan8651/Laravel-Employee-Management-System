<?php

namespace App\Services;

use App\Events\EmployeeCreated;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\PasswordSetupNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class EmployeeService
{
    /**
     * Create an employee and a linked user account.
     *
     * Every employee receives a login account.
     * The selected role determines the user's permissions.
     */
    public function createEmployee(
        array $data,
        ?UploadedFile $profilePhoto = null
    ): Employee {

        $role = $data['role'] ?? 'employee';

        // These fields belong to account creation or file handling,
        // not directly to the employees table.
        unset(
            $data['role'],
            $data['create_user_account'],
            $data['profile_photo']
        );

        $photoPath = null;

        try {
            // Store profile photo if provided.
            if ($profilePhoto !== null) {
                $photoPath = $profilePhoto->store(
                    'employee-photos',
                    'private_photos'
                );

                if ($photoPath === false) {
                    throw new \RuntimeException(
                        'Failed to store employee profile photo.'
                    );
                }

                $data['profile_photo'] = $photoPath;
            }

            // Create User and Employee together.
            $employee = DB::transaction(function () use (
                &$data,
                $role
            ) {

                // Prevent duplicate login accounts.
                if (User::where('email', $data['email'])->exists()) {
                    throw ValidationException::withMessages([
                        'email' => 'This email already belongs to a login account. Please use another email.',
                    ]);
                }

                // 1. Create User account.
                $user = User::create([
                    'email' => $data['email'],
                    'password' => Hash::make(Str::random(64)),
                ]);

                // Assign the selected role explicitly.
                $user->role = $role;
                $user->save();

                // 2. Link Employee to User.
                $data['user_id'] = $user->getKey();

                // 3. Create Employee record.
                return Employee::create($data);
            });

            // Dispatch the event after the database transaction commits.
            event(new EmployeeCreated($employee));

            return $employee;

        } catch (Throwable $exception) {

            // Remove uploaded photo if database operation fails.
            if ($photoPath !== null) {
                Storage::disk('private_photos')->delete($photoPath);
            }

            throw $exception;
        }
    }

    /**
     * Send password setup instructions to the employee.
     */
    public function sendPasswordSetupLink(Employee $employee): string
    {
        $status = Password::sendResetLink(
            ['email' => $employee->email],
            function (User $user, string $token) use ($employee) {
                $user->notify(
                    new PasswordSetupNotification(
                        $token,
                        $employee
                    )
                );
            }
        );

        if ($status !== Password::RESET_LINK_SENT) {
            Log::warning(
                'Password setup email was not sent.',
                [
                    'employee_id' => $employee->id,
                    'status' => $status,
                ]
            );
        }

        return $status;
    }
}
