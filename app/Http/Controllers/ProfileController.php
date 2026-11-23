<?php

namespace App\Http\Controllers;

use App\Services\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Throwable;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load([
            'employee.department',
            'profile',
        ]);

        return view('profile.show', compact('user'));
    }

    public function update(
        Request $request,
        AdminNotificationService $adminNotificationService
    ) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
            'profile_photo' => [
                'nullable',
                'image',
                'mimes:'.implode(',', config('ems.upload.allowed_image_types')),
                'max:'.config('ems.upload.max_size'),
                'dimensions:max_width=3000,max_height=3000',
            ],
        ]);

        $user = $request->user();
        $employee = $user->employee;
        $changedFields = [];
        $newPhotoPath = null;

        $oldPhotoPath = $employee
            ? $employee->profile_photo
            : $user->profile_photo;

        $photoDirectory = $employee
            ? 'employee-photos'
            : 'user-profile-photos';

        try {
            if ($request->hasFile('profile_photo')) {
                $newPhotoPath = $request->file('profile_photo')
                    ->store($photoDirectory, 'private_photos');

                if ($newPhotoPath === false) {
                    throw new \RuntimeException(
                        'Failed to store profile photo.'
                    );
                }
            }

            if ($employee) {
                if ($employee->phone !== ($validated['phone'] ?? null)) {
                    $changedFields[] = 'Phone number';
                }

                if ($employee->address !== ($validated['address'] ?? null)) {
                    $changedFields[] = 'Address';
                }

                if ($request->hasFile('profile_photo')) {
                    $changedFields[] = 'Profile photo';
                }
            }

            DB::transaction(function () use (
                $user,
                $employee,
                $validated,
                $newPhotoPath
            ) {
                if ($employee) {
                    // Employee personal information belongs to employees.
                    $employee->name = $validated['name'];
                    $employee->phone = $validated['phone'] ?? null;
                    $employee->address = $validated['address'] ?? null;

                    if ($newPhotoPath !== null) {
                        $employee->profile_photo = $newPhotoPath;
                    }

                    $employee->save();
                } else {
                    // Standalone user profile information belongs to user_profiles.
                    $profile = $user->profile()->firstOrNew();

                    $profile->name = $validated['name'];
                    $profile->phone = $validated['phone'] ?? null;
                    $profile->address = $validated['address'] ?? null;

                    if ($newPhotoPath !== null) {
                        $profile->profile_photo = $newPhotoPath;
                    }

                    $profile->save();
                }
            });

            if ($employee && ! empty($changedFields)) {
                try {
                    $adminNotificationService->notifyAdmins(
                        $employee->fresh(),
                        'updated',
                        $changedFields,
                        $user->id
                    );
                } catch (Throwable $notificationException) {
                    report($notificationException);

                    \Log::warning('Admin notification for employee self-profile update failed.', [
                        'employee_id' => $employee->id,
                        'error' => $notificationException->getMessage(),
                    ]);
                }
            }

            // Delete the previous photo only after the DB transaction succeeds.
            if (
                $newPhotoPath !== null &&
                $oldPhotoPath !== null &&
                $oldPhotoPath !== $newPhotoPath
            ) {
                $allowedOldDirectories = $employee
                    ? ['employee-photos/', 'user-profile-photos/']
                    : ['user-profile-photos/'];

                foreach ($allowedOldDirectories as $directory) {
                    if (str_starts_with($oldPhotoPath, $directory)) {
                        try {
                            Storage::disk('private_photos')->delete($oldPhotoPath);
                        } catch (Throwable $cleanupException) {
                            // Keep the new photo even if old-photo cleanup fails.
                            report($cleanupException);
                        }

                        break;
                    }
                }
            }

            return redirect()
                ->route('employee.profile.show')
                ->with('success', 'Profile updated successfully.');

        } catch (Throwable $exception) {
            if ($newPhotoPath !== null) {
                Storage::disk('private_photos')->delete($newPhotoPath);
            }

            report($exception);

            return back()
                ->withInput($request->except('profile_photo'))
                ->with(
                    'error',
                    'Unable to update your profile. Please try again.'
                );
        }
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        // Verify the current password first.
        if (
            ! $request->filled('current_password') ||
            ! Hash::check(
                $request->input('current_password'),
                $user->password
            )
        ) {
            return back()
                ->withErrors([
                    'current_password' => 'The current password is incorrect.',
                ], 'passwordChange');
        }

        $validated = $request->validateWithBag(
            'passwordChange',
            [
                'password' => [
                    'required',
                    'string',
                    'confirmed',
                    Password::min(12)
                        ->mixedCase()
                        ->numbers(),
                ],
            ],
            [
                'password.required' => 'Please enter a new password.',
                'password.confirmed' => 'The new password and confirmation is not matching.',
                'password.min' => 'The new password must be at least 12 characters.',
            ]
        );

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()
            ->route('employee.profile.show')
            ->with('success', 'Your password has been updated successfully.');
    }
}
