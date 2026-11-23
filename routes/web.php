<?php

use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', fn () => redirect()->route('login'));

// Dashboard
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Admin routes
Route::middleware([
    'auth',
    'verified',
    'admin',
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Employee status
        Route::patch(
            '/employees/{employee}/toggle-status',
            [EmployeeController::class, 'toggleStatus']
        )->whereNumber('employee')
            ->name('employees.toggle-status');

        // Employee management
        Route::resource('employees', EmployeeController::class)
            ->whereNumber('employee');

        // Department status
        Route::patch(
            '/departments/{department}/toggle-status',
            [DepartmentController::class, 'toggleStatus']
        )->whereNumber('department')
            ->name('departments.toggle-status');

        // Department management
        Route::resource('departments', DepartmentController::class)
            ->whereNumber('department');

        // User management
        Route::resource('users', UserController::class)
            ->whereNumber('user')
            ->middleware('can:manage-users');

        // Reports
        Route::get('/reports', [
            ReportController::class,
            'index',
        ])->name('reports.index');

        Route::get('/reports/export', [
            ReportController::class,
            'export',
        ])->name('reports.export');
    });

// Employee profile
Route::middleware([
    'auth',
    'verified',
    'active',
])
    ->prefix('employee')
    ->name('employee.')
    ->group(function () {

        Route::get('/profile', [
            ProfileController::class,
            'show',
        ])->name('profile.show');

        Route::put('/profile', [
            ProfileController::class,
            'update',
        ])->name('profile.update');

        Route::put('/profile/password', [
            ProfileController::class,
            'changePassword',
        ])->name('profile.password');
    });

// Mark a notification as read.
Route::post('/notifications/{notification}/read', function (
    Request $request,
    string $notification
) {
    $userNotification = $request->user()
        ->notifications()
        ->findOrFail($notification);

    $userNotification->markAsRead();

    return back();
})->middleware(['auth', 'verified'])->name('notifications.read');

// Protected profile photo routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Employee photos: Admin or the employee themselves.
    Route::get(
        '/profile-photos/employees/{employee}',
        [ProfilePhotoController::class, 'employee']
    )
        ->whereNumber('employee')
        ->name('profile-photos.employee');

    // Standalone user photos: Admin only.
    Route::get(
        '/profile-photos/users/{user}',
        [ProfilePhotoController::class, 'user']
    )
        ->whereNumber('user')
        ->name('profile-photos.user');
});

// Login
Route::get('/login', [
    AuthController::class,
    'showLoginForm',
])->middleware('guest')->name('login');

Route::post('/login', [
    AuthController::class,
    'login',
])->middleware('guest')->name('login.store');

// Logout
Route::post('/logout', [
    AuthController::class,
    'logout',
])->middleware('auth')->name('logout');

// Password reset
Route::middleware('guest')->group(function () {

    Route::get('/forgot-password', [
        PasswordResetController::class,
        'showLinkRequestForm',
    ])->name('password.request');

    Route::post('/forgot-password', [
        PasswordResetController::class,
        'sendResetLinkEmail',
    ])->name('password.email');

    Route::get('/reset-password/{token}', [
        PasswordResetController::class,
        'showResetForm',
    ])->name('password.reset');

    Route::post('/reset-password', [
        PasswordResetController::class,
        'reset',
    ])->name('password.update');
});

// Email verification notice
Route::get('/email/verify', fn () => view('auth.verify-email'))
    ->middleware('auth')
    ->name('verification.notice');

// Verify email using Laravel's signed verification link.
// The user does not need to be logged in to verify.
Route::get('/email/verify/{id}/{hash}', function (
    string $id,
    string $hash
) {
    $user = User::findOrFail($id);

    // Validate that the URL hash matches the user's email.
    if (! hash_equals(
        sha1($user->getEmailForVerification()),
        $hash
    )) {
        abort(403, 'Invalid email verification link.');
    }

    // Mark the email as verified once.
    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    return redirect()
        ->route('login')
        ->with(
            'success',
            'Email verified successfully. You can now sign in.'
        );
})->middleware([
    'signed',
    'throttle:6,1',
])->name('verification.verify');

// Resend verification email
Route::post('/email/verification-notification', function (
    Request $request
) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()
            ->route('dashboard')
            ->with('status', 'Your email is already verified.');
    }

    $request->user()->sendEmailVerificationNotification();

    return back()->with(
        'status',
        'A new verification link has been sent to your email address.'
    );
})->middleware([
    'auth',
    'throttle:6,1',
])->name('verification.send');

// Employee lookup: verified administrators only
Route::bind('employeeCode', function (string $value) {
    return Employee::where('employee_code', $value)->firstOrFail();
});

Route::middleware([
    'auth',
    'verified',
    'can:manage-employees',
])->get(
    '/admin/employees/code/{employeeCode}',
    fn (Employee $employeeCode) => response()->json($employeeCode)
)->name('admin.employees.by-code');

// 404 fallback — keep this last
Route::fallback(fn () => response()->view('errors.404', [], 404));
