<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use App\Services\EmployeeService;
use App\Contracts\EmployeeRepositoryInterface;
use App\Notifications\EmployeeUpdatedNotification;
use App\Services\AdminNotificationService;

class EmployeeController extends Controller
{
    /**
     * Constructor dependency injection.
     *
     * Laravel automatically injects the repository implementation
     * registered in AppServiceProvider.
     */
    public function __construct(
        private EmployeeRepositoryInterface $employeeRepository
    ) {
    }

    /**
     * Display employees with filtering, sorting and pagination.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Employee::class);

        $filterKeys = [
            'search',
            'department_id',
            'status',
            'gender',
            'sort',
            'direction',
        ];

        /*
         * Reset session filters and expire the preference cookie.
         * The redirect must not save the cookie again.
         */
        if ($request->boolean('reset')) {
            $request->session()->forget('employee_filters');

            return redirect()
                ->route('admin.employees.index')
                ->withCookie(
                    cookie()->forget(
                        'employee_filter_preference',
                        config('session.path', '/'),
                        config('session.domain')
                    )
                );
        }

        $hasFilterParameters = collect($filterKeys)
            ->contains(fn ($key) => $request->query->has($key));

        /*
         * Restore saved preferences only when the URL does not
         * explicitly provide filter parameters.
         */
        if (!$hasFilterParameters) {
            $savedFilters = $request->session()->get(
                'employee_filters',
                []
            );

            if (!is_array($savedFilters) || empty($savedFilters)) {
                $cookieValue = $request->cookie(
                    'employee_filter_preference'
                );

                $cookieFilters = is_string($cookieValue)
                    ? json_decode($cookieValue, true)
                    : null;

                $savedFilters = is_array($cookieFilters)
                    ? $cookieFilters
                    : [];
            }

            foreach ($filterKeys as $key) {
                if (array_key_exists($key, $savedFilters)) {
                    $request->query->set($key, $savedFilters[$key]);
                }
            }
        }

        // Validate filter parameters.
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],

            'department_id' => [
                'nullable',
                'integer',
                'exists:departments,id',
            ],

            'status' => [
                'nullable',
                Rule::in(['active', 'inactive', 'terminated']),
            ],

            'gender' => [
                'nullable',
                Rule::in(['male', 'female', 'other']),
            ],

            'sort' => [
                'nullable',
                Rule::in([
                    'name',
                    'employee_code',
                    'email',
                    'date_of_joining',
                    'created_at',
                ]),
            ],

            'direction' => [
                'nullable',
                Rule::in(['asc', 'desc']),
            ],
        ]);

        $filters = [
            'search' => $validated['search'] ?? '',
            'department_id' => $validated['department_id'] ?? '',
            'status' => $validated['status'] ?? '',
            'gender' => $validated['gender'] ?? '',
            'sort' => $validated['sort'] ?? 'name',
            'direction' => $validated['direction'] ?? 'asc',
        ];

        $sortOptions = [
            'name' => 'name',
            'employee_code' => 'employee_code',
            'email' => 'email',
            'date_of_joining' => 'date_of_joining',
            'created_at' => 'created_at',
        ];

        $sortBy = $filters['sort'];
        $direction = $filters['direction'];

        if (!isset($sortOptions[$sortBy])) {
            $sortBy = 'name';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        $filters['sort'] = $sortBy;
        $filters['direction'] = $direction;

        /*
         * Save normalized filters in the session.
         */
        $request->session()->put('employee_filters', $filters);

        /*
         * Only persist meaningful preferences.
         * Default values alone should not recreate a cookie after reset.
         */
        $hasPreferences =
            $filters['search'] !== ''
            || $filters['department_id'] !== ''
            || $filters['status'] !== ''
            || $filters['gender'] !== ''
            || $filters['sort'] !== 'name'
            || $filters['direction'] !== 'asc';

        /*
         * Repository Pattern:
         * Database filtering, sorting and pagination are handled
         * by EloquentEmployeeRepository instead of this controller.
         */
        $employees = $this->employeeRepository
            ->paginateFiltered(
                $filters,
                $sortOptions[$sortBy],
                $direction
            )
            ->withQueryString();

        // Retrieve departments for the filter dropdown.
        $departments = Department::orderBy('name')->get();

        $response = response()->view(
            'admin.employees.index',
            compact(
                'employees',
                'departments',
                'sortBy',
                'direction',
                'filters'
            )
        );

        /*
         * Set or expire the cookie according to the current filters.
         * Laravel encrypts application cookies through its middleware.
         */
        if ($hasPreferences) {
            $filterCookie = cookie(
                'employee_filter_preference',
                json_encode($filters),
                60 * 24 * 30
            );

            $response->withCookie($filterCookie);
        } else {
            $response->withCookie(
                cookie()->forget('employee_filter_preference')
            );
        }

        return $response;
    }

    /**
     * Show the employee creation form.
     */
    public function create()
    {
        Gate::authorize('create', Employee::class);

        $departments = Department::orderBy('name')->get();

        return view(
            'admin.employees.create',
            compact('departments')
        );
    }

    /**
     * Store a new employee and linked user account.
     */
    public function store(
        StoreEmployeeRequest $request,
        EmployeeService $employeeService,
        AdminNotificationService $adminNotificationService
    ) {
        try {
            // Create Employee and linked User account.
            $employee = $employeeService->createEmployee(
                $request->validated(),
                $request->file('profile_photo')
            );

            try {
                $adminNotificationService->notifyAdmins(
                    $employee,
                    'created',
                    [],
                    auth()->id()
                );
            } catch (Throwable $notificationException) {
                report($notificationException);

                Log::warning('Admin employee creation notification failed.', [
                    'employee_id' => $employee->id,
                    'error' => $notificationException->getMessage(),
                ]);
            }

            $message = 'Employee and linked login account created successfully. Password setup instructions have been queued for delivery.';

            return redirect()
                ->route('admin.employees.show', $employee)
                ->with('success', $message);

        } catch (Throwable $exception) {

            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                throw $exception;
            }

            report($exception);

            return back()
                ->withInput($request->except('profile_photo'))
                ->with(
                    'error',
                    'Unable to create the employee. Please check the details and try again.'
                );
        }
    }

    /**
     * Display a specific employee.
     */
    public function show(Employee $employee)
    {
        Gate::authorize('view', $employee);

        $employee->load('department');

        $activityLogs = $employee->activityLogs()
            ->with('user')
            ->latest()
            ->get();

        return view(
            'admin.employees.show',
            compact('employee', 'activityLogs')
        );
    }

    /**
     * Show the employee edit form.
     */
    public function edit(Employee $employee)
    {
        Gate::authorize('update', $employee);

        $departments = Department::orderBy('name')->get();

        return view(
            'admin.employees.edit',
            compact('employee', 'departments')
        );
    }

    /**
     * Update an existing employee.
     */
    public function update(
        UpdateEmployeeRequest $request,
        Employee $employee,
        AdminNotificationService $adminNotificationService
    ) {
        $validated = $request->validated();

        $oldPhotoPath = $employee->profile_photo;
        $newPhotoPath = null;

        try {
            if ($request->hasFile('profile_photo')) {
                $newPhotoPath = $request->file('profile_photo')
                    ->store('employee-photos', 'private_photos');

                if ($newPhotoPath === false) {
                    throw new \RuntimeException(
                        'Failed to store employee profile photo.'
                    );
                }

                $validated['profile_photo'] = $newPhotoPath;
            }

            $updated = $employee->update($validated);

            if (!$updated) {
                throw new \RuntimeException(
                    'Failed to update employee record.'
                );
            }

            $changedFields = collect($employee->getChanges())
                ->keys()
                ->reject(fn ($field) => $field === 'updated_at')
                ->values()
                ->all();

            // Notify Admins about the employee update.
            if (!empty($changedFields)) {
                try {
                    $adminNotificationService->notifyAdmins(
                        $employee->fresh(),
                        'updated',
                        $changedFields,
                        auth()->id()
                    );
                } catch (Throwable $notificationException) {
                    report($notificationException);

                    Log::warning('Admin employee update notification failed.', [
                        'employee_id' => $employee->id,
                        'error' => $notificationException->getMessage(),
                    ]);
                }
            }
        } catch (Throwable $exception) {

            // Delete the new upload only if the database update fails.
            if ($newPhotoPath !== null) {
                Storage::disk('private_photos')->delete($newPhotoPath);
            }

            report($exception);

            return back()
                ->withInput($request->except('profile_photo'))
                ->with(
                    'error',
                    'Unable to update the employee. Please try again.'
                );
        }

        // Clean up the old photo separately.
        if (
            $newPhotoPath !== null &&
            $oldPhotoPath !== null &&
            $oldPhotoPath !== $newPhotoPath
        ) {
            try {
                Storage::disk('private_photos')->delete($oldPhotoPath);
            } catch (Throwable $cleanupException) {
                report($cleanupException);
            }
        }

        // Notify the employee if profile information actually changed.
        if (!empty($changedFields) && $employee->user) {
            try {
                $employee->user->notify(
                    new EmployeeUpdatedNotification(
                        $employee,
                        $changedFields,
                        auth()->user()?->name
                    )
                );
            } catch (Throwable $notificationException) {
                report($notificationException);

                Log::warning('Employee update notification failed.', [
                    'employee_id' => $employee->id,
                    'error' => $notificationException->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('admin.employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Toggle an employee between Active and Inactive.
     * Terminated employees cannot be toggled.
     */
    public function toggleStatus(
        Employee $employee,
        AdminNotificationService $adminNotificationService
    )
    {
        Gate::authorize('update', $employee);

        $currentStatus = $employee->status?->value;

        if ($currentStatus === 'terminated') {
            return redirect()
                ->route('admin.employees.index')
                ->with(
                    'error',
                    'Terminated employees cannot be activated or deactivated using this action.'
                );
        }

        $newStatus = $currentStatus === 'active'
            ? 'inactive'
            : 'active';

        $employee->update([
            'status' => $newStatus,
        ]);

        // Notify Admins about the status change.
        try {
            $adminNotificationService->notifyAdmins(
                $employee->fresh(),
                'status_changed',
                ['status'],
                auth()->id()
            );
        } catch (Throwable $notificationException) {
            report($notificationException);

            Log::warning('Admin employee status notification failed.', [
                'employee_id' => $employee->id,
                'error' => $notificationException->getMessage(),
            ]);
        }

        // Notify the employee about the status change.
        if ($employee->user) {
            try {
                $employee->user->notify(
                    new EmployeeUpdatedNotification(
                        $employee,
                        ['status'],
                        auth()->user()?->name
                    )
                );
            } catch (Throwable $notificationException) {
                report($notificationException);

                Log::warning('Employee status notification failed.', [
                    'employee_id' => $employee->id,
                    'error' => $notificationException->getMessage(),
                ]);
            }
        }

        $message = $newStatus === 'active'
            ? 'Employee activated successfully.'
            : 'Employee deactivated successfully.';

        return redirect()
            ->route('admin.employees.index')
            ->with('success', $message);
    }

    /**
     * Delete an employee and the linked user account.
     */
    public function destroy(
        Employee $employee,
        AdminNotificationService $adminNotificationService
    )
    {
        Gate::authorize('delete', $employee);

        $photoPath = $employee->profile_photo;
        $user = $employee->user;

        $deletionDetails = [
            'employee_id' => $employee->id,
            'employee_code' => $employee->employee_code,
            'employee_name' => $employee->name,
            'deleted_by_user_id' => auth()->id(),
        ];

        // Perform database deletion first.
        try {
            DB::transaction(function () use ($employee, $user) {
                $employee->delete();

                if ($user) {
                    $user->delete();
                }
            });
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.employees.index')
                ->with(
                    'error',
                    'Unable to delete the employee and linked user account. Please try again.'
                );
        }

        // Notify Admins after successful employee deletion.
        try {
            $adminNotificationService->notifyAdmins(
                $employee,
                'deleted',
                [],
                auth()->id()
            );
        } catch (Throwable $notificationException) {
            report($notificationException);

            Log::warning('Admin employee deletion notification failed.', [
                'employee_id' => $employee->id,
                'error' => $notificationException->getMessage(),
            ]);
        }

        // Clean up the profile photo separately.
        if ($photoPath) {
            try {
                Storage::disk('private_photos')->delete($photoPath);
            } catch (Throwable $cleanupException) {
                report($cleanupException);

                Log::warning(
                    'Employee deleted, but profile photo cleanup failed.',
                    $deletionDetails + [
                        'photo_path' => $photoPath,
                    ]
                );
            }
        }

        Log::channel('daily')->info(
            'Employee deleted successfully',
            $deletionDetails
        );

        return redirect()
            ->route('admin.employees.index')
            ->with(
                'success',
                'Employee and linked user account deleted successfully.'
            );
    }
}






