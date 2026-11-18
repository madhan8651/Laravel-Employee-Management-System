<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', User::class);

        $users = User::query()
            ->with([
                'profile',
                'employeeIncludingTrashed',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        Gate::authorize('create', User::class);
        return view('admin.users.create');
    }
public function store(StoreUserRequest $request)
{
    $validated = $request->validated();

    $password = Str::random(64);

    $user = DB::transaction(function () use ($validated, $password) {

        // Create the account.
        // Personal information is stored separately in user_profiles.
        $user = User::create([
            'email' => $validated['email'],
            'password' => $password,
        ]);

        // Assign account role.
        $user->role = $validated['role'];
        $user->save();

        // Create the new personal profile.
        $user->profile()->create([
            'name' => $validated['name'],
        ]);

        return $user;
    });

    // Send password setup instructions.
    $resetStatus = Password::sendResetLink([
        'email' => $user->email,
    ]);

    $message = $resetStatus === Password::RESET_LINK_SENT
        ? 'User created successfully. Password setup instructions have been sent.'
        : 'User created, but the password setup email could not be sent. Please check the mail configuration and try again.';

    return redirect()
        ->route('admin.users.show', $user)
        ->with('success', $message);
}

public function show(User $user)
{
    Gate::authorize('view', $user);

    $user->load([
        'profile',
        'employeeIncludingTrashed',
    ]);

    return view('admin.users.show', compact('user'));
}


    public function edit(User $user)
    {
        Gate::authorize('update', $user);

        $user->load([
            'employee',
            'profile',
        ]);

        return view('admin.users.edit', compact('user'));
    }

    
public function update(
    UpdateUserRequest $request,
    User $user
) {
    $validated = $request->validated();

    DB::transaction(function () use ($validated, $user) {

        // Update account information.
        $user->email = $validated['email'];

        // Update password only when a new password is provided.
        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        // Check for an employee, including soft-deleted records.
        /** @var \App\Models\Employee|null $employee */
        $employee = $user->employeeIncludingTrashed()->first();

        if ($employee) {

            // Employee personal information belongs in employees.
            $employee->name = $validated['name'];
            $employee->save();

        } else {

            // Standalone user personal information belongs in user_profiles.
            $user->profile()->updateOrCreate([], [
                'name' => $validated['name'],
            ]);
        }
    });

    return redirect()
        ->route('admin.users.show', $user)
        ->with('success', 'User updated successfully.');
}

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        if (auth()->id() === $user->getKey()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Retrieve employee, including soft-deleted records.
        /** @var \App\Models\Employee|null $employee */
        $employee = $user->employeeIncludingTrashed()->first();

        // Capture photo paths from their actual owning records.
        $profilePhotoPath = $user->profile()->value('profile_photo');
        $employeePhotoPath = $employee?->profile_photo;

        try {
            DB::transaction(function () use ($user, $employee) {
                // Soft-delete only active employee records.
                if ($employee && !$employee->trashed()) {
                    $employee->delete();
                }

                // Delete the user account.
                $user->delete();
            });

            // Delete physical photos only after the transaction succeeds.
            $privateDisk = Storage::disk('private_photos');
            $photoPaths = array_unique(array_filter([
                $employeePhotoPath,
                $profilePhotoPath,
            ]));

            foreach ($photoPaths as $photoPath) {
                if ($privateDisk->exists($photoPath)) {
                    $privateDisk->delete($photoPath);
                }
            }

            return redirect()
                ->route('admin.users.index')
                ->with(
                    'success',
                    'User and linked employee record deleted successfully.'
                );

        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.users.index')
                ->with(
                    'error',
                    'Unable to delete the user and linked employee record. Please try again.'
                );
        }

    }
}
