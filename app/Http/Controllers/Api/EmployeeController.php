<?php

namespace App\Http\Controllers\Api;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeCollection;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Throwable;

class EmployeeController extends Controller
{
    /**
     * Display employees with search, filters and pagination.
     */
    public function index(Request $request): EmployeeCollection
    {
        // Validate API filter parameters.
        $validator = Validator::make($request->query(), [
            'search' => ['nullable', 'string', 'max:255'],

            'department_id' => [
                'nullable',
                'integer',
                'exists:departments,id',
            ],

            'status' => [
                'nullable',
                'in:active,inactive,terminated',
            ],

            'gender' => [
                'nullable',
                'in:male,female,other',
            ],

            'sort' => [
                'nullable',
                'in:name,employee_code,email,date_of_joining,created_at',
            ],

            'direction' => [
                'nullable',
                'in:asc,desc',
            ],

            'per_page' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                'max:100',
            ],
        ]);

        $validated = $validator->validate();

        $user = $request->user();

        $query = Employee::query()->with('department');

        // Employees can access only their own record.
        if ($user->role !== Role::Admin) {
            $query->where('user_id', $user->id);
        }

        // Search by employee name, email or employee code.
        if (!empty($validated['search'])) {
            $query->search($validated['search']);
        }

        // Filter by department.
        if (!empty($validated['department_id'])) {
            $query->where('department_id', $validated['department_id']);
        }

        // Filter by employment status.
        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        // Filter by gender.
        if (!empty($validated['gender'])) {
            $query->where('gender', $validated['gender']);
        }

        // Use configured default when per_page is omitted.
        $perPage = $validated['per_page']
            ?? config('ems.pagination.api_employees', 10);

        $sort = $validated['sort'] ?? 'id';
        $direction = $validated['direction'] ?? 'asc';

        $employees = $query
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        return new EmployeeCollection($employees);
    }

    /**
     * Display one employee.
     */
    public function show(Employee $employee): EmployeeResource
    {
        Gate::authorize('view', $employee);

        $employee->load('department');

        return (new EmployeeResource($employee))->additional([
            'status' => true,
            'message' => 'Employee retrieved successfully',
        ]);
    }

    /**
     * Create an employee and linked user account.
     */
    public function store(
        StoreEmployeeRequest $request,
        EmployeeService $employeeService
    ): JsonResponse {
        $validated = $request->validated();

        $employee = $employeeService->createEmployee(
            $validated,
            $request->file('profile_photo')
        );

        $employee->load('department');

        return (new EmployeeResource($employee))->additional([
            'status' => true,
            'message' => 'Employee created successfully',
        ])->response()->setStatusCode(201);
    }

    /**
     * Update an employee.
     */
    public function update(
        UpdateEmployeeRequest $request,
        Employee $employee
    ): EmployeeResource {
        $validated = $request->validated();

        $oldPhotoPath = $employee->profile_photo;
        $newPhotoPath = null;

        // Handle a newly uploaded profile photo.
        if ($request->hasFile('profile_photo')) {
            $newPhotoPath = $request->file('profile_photo')
                ->store('employee-photos', 'private_photos');

            if ($newPhotoPath === false) {
                throw new \RuntimeException(
                    'Failed to store employee profile photo.'
                );
            }

            $validated['profile_photo'] = $newPhotoPath;
        } else {
            // Preserve the existing photo when no new file is uploaded.
            unset($validated['profile_photo']);
        }

        try {
            $updated = $employee->update($validated);

            if (!$updated) {
                throw new \RuntimeException(
                    'Failed to update employee record.'
                );
            }
        } catch (Throwable $exception) {
            // Remove the new upload only if the database update fails.
            if ($newPhotoPath !== null) {
                Storage::disk('private_photos')->delete($newPhotoPath);
            }

            throw $exception;
        }

        // Delete the old photo separately after a successful update.
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

        $employee->refresh()->load('department');

        return (new EmployeeResource($employee))->additional([
            'status' => true,
            'message' => 'Employee updated successfully',
        ]);
    }

    /**
     * Delete an employee using soft deletion.
     */
    public function destroy(Employee $employee): JsonResponse
    {
        Gate::authorize('delete', $employee);

        $employee->delete();

        return response()->json([
            'status' => true,
            'message' => 'Employee deleted successfully.',
        ], 200);
    }
}