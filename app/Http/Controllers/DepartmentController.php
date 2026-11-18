<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Exceptions\DepartmentHasEmployeesException;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use Illuminate\Support\Facades\Gate;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Department::class);

        // Reset only Department Management filters.
        if ($request->boolean('reset')) {
            $request->session()->forget('department_filters');

            if ($request->ajax()) {
                $filters = [
                    'search' => '',
                    'status' => '',
                ];
            } else {
                return redirect()->route('admin.departments.index');
            }
        }

        // Detect submitted filter parameters.
        $hasFilterParams = $request->query->has('search')
            || $request->query->has('status');

        if ($hasFilterParams) {
            $validated = $request->validate([
                'search' => ['nullable', 'string', 'max:255'],
                'status' => ['nullable', 'in:active,inactive'],
            ]);

            $filters = [
                'search' => trim($validated['search'] ?? ''),
                'status' => $validated['status'] ?? '',
            ];

            if ($filters['search'] !== '' || $filters['status'] !== '') {
                $request->session()->put('department_filters', $filters);
            } else {
                $request->session()->forget('department_filters');
            }
        } else {
            // Restore saved Department Management filters.
            $filters = $request->session()->get('department_filters', [
                'search' => '',
                'status' => '',
            ]);
        }

        $departments = Department::query()
            ->withCount('employees')
            ->withSum('employees', 'salary')
            ->withAvg('employees', 'salary')
            ->when($filters['search'] ?? '', function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? '', function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('name')
            ->paginate((int) config('ems.pagination.departments', 10))
            ->appends(array_filter(
                $filters,
                fn ($value) => $value !== ''
            ));

        // Return partial HTML for AJAX requests.
        if ($request->ajax()) {
            return response()->json([
                'html' => view(
                    'admin.departments.partials.table',
                    compact('departments')
                )->render(),
            ]);
        }

        // Return complete page for normal requests.
        return view('admin.departments.index', compact(
            'departments',
            'filters'
        ));
    }

    public function create()
    {
        Gate::authorize('create', Department::class);

        return view('admin.departments.create');
    }

    public function store(StoreDepartmentRequest $request)
    {
        $validated = $request->validated();

        $department = Department::create($validated);

        return redirect()
            ->route('admin.departments.show', $department)
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        Gate::authorize('view', $department);

        $department->load('employees');

        return view('admin.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        Gate::authorize('update', $department);

        return view('admin.departments.edit', compact('department'));
    }

    public function update(
        UpdateDepartmentRequest $request,
        Department $department
    ) {
        $validated = $request->validated();

        $department->update($validated);

        return redirect()
            ->route('admin.departments.show', $department)
            ->with('success', 'Department updated successfully.');
    }

    // Activate or deactivate a department.
    public function toggleStatus(Department $department)
    {
        Gate::authorize('update', $department);

        $status = $department->getAttribute('status') === 'active'
            ? 'inactive'
            : 'active';

        $department->setAttribute('status', $status);
        $department->save();

        return redirect()
            ->route('admin.departments.index')
            ->with(
                'success',
                "Department {$status} successfully."
            );
    }

    public function destroy(Department $department)
    {
        Gate::authorize('delete', $department);

        if ($department->employees()->exists()) {
            throw new DepartmentHasEmployeesException();
        }

        $department->delete();

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}


