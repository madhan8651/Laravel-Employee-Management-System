@extends('layouts.app')

@section('title', 'Department Details')

@section('content')
<div class="container-fluid px-0">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title">Department Details</h1>
            <p class="page-subtitle mb-0">
                View department information and its assigned employees.
            </p>
        </div>

    </div>

    {{-- Department Information --}}
    <div class="form-card mb-4">

        {{-- Department Heading --}}
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4 pb-4 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="rounded-circle bg-primary-subtle text-primary
                                d-flex align-items-center justify-content-center"
                         style="width: 48px; height: 48px;">
                        <i class="bi bi-building fs-4"></i>
                    </div>

                    <h2 class="h4 fw-bold mb-0">
                        {{ $department->name }}
                    </h2>
                </div>

                <p class="text-secondary mb-0">
                    Department information and employee allocation.
                </p>
            </div>

            <div class="d-flex flex-column align-items-start align-items-sm-end gap-2">
                <span class="badge rounded-pill bg-light text-secondary border px-3 py-2">
                    <i class="bi bi-clock-history me-1"></i>
                    Last Updated: {{ $department->updated_at?->format('M d, Y h:i A') ?? '—' }}
                </span>
            </div>
        </div>

        {{-- Department Details --}}
        <div class="mb-4">
            <h3 class="h5 fw-bold mb-1">Department Information</h3>
            <p class="text-secondary mb-0">
                Overview of the department and its current team size.
            </p>
        </div>

        <div class="row g-4">

            {{-- Department Name --}}
            <div class="col-12 col-md-6">
                <div class="border-bottom pb-3 h-100">
                    <div class="small fw-semibold text-secondary mb-2">
                        <i class="bi bi-tag me-1"></i>
                        Department Name
                    </div>

                    <div class="fw-medium text-break">
                        {{ $department->name }}
                    </div>
                </div>
            </div>

            {{-- Total Employees --}}
            <div class="col-12 col-md-6">
                <div class="border-bottom pb-3 h-100">
                    <div class="small fw-semibold text-secondary mb-2">
                        <i class="bi bi-people me-1"></i>
                        Total Employees
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                            {{ $department->employees_count ?? $department->employees()->count() }}
                        </span>

                        <span class="text-secondary small">
                            {{ ($department->employees_count ?? $department->employees()->count()) === 1
                                ? 'employee assigned'
                                : 'employees assigned' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="col-12">
                <div class="border-bottom pb-3">
                    <div class="small fw-semibold text-secondary mb-2">
                        <i class="bi bi-card-text me-1"></i>
                        Description
                    </div>

                    <div class="text-break" style="white-space: pre-line;">{{ $department->description ?: 'No description provided.' }}</div>
                </div>
            </div>

        </div>

        {{-- Department Actions --}}
        <div class="d-flex flex-wrap gap-2 mt-4 pt-4 border-top">
            <a href="{{ route('admin.departments.edit', $department) }}"
               class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i>
                Edit Department
            </a>

            <a href="{{ route('admin.departments.index') }}"
               class="btn btn-light border">
                Back to Departments
            </a>
        </div>

    </div>

    {{-- Employees in This Department --}}
    <div class="form-card">

        {{-- Section Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-3 border-bottom">
            <div>
                <h2 class="h5 fw-bold mb-1">
                    <i class="bi bi-people me-2 text-primary"></i>
                    Employees in This Department
                </h2>

                <p class="text-secondary mb-0">
                    View the employees currently assigned to this department.
                </p>
            </div>

            <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">
                {{ $department->employees->count() }}
                {{ $department->employees->count() === 1 ? 'Employee' : 'Employees' }}
            </span>
        </div>

        @if ($department->employees->isEmpty())

            {{-- Empty State --}}
            <div class="text-center py-5">
                <div class="rounded-circle bg-light d-inline-flex align-items-center
                            justify-content-center mb-3"
                     style="width: 64px; height: 64px;">
                    <i class="bi bi-people text-secondary fs-2"></i>
                </div>

                <h3 class="h6 fw-semibold mb-2">
                    No Employees Assigned
                </h3>

                <p class="text-secondary small mb-3">
                    Assign employees to this department from the employee edit page.
                </p>

                <a href="{{ route('admin.employees.index') }}"
                   class="btn btn-primary">
                    <i class="bi bi-person-lines-fill me-1"></i>
                    View Employees
                </a>
            </div>

        @else

            {{-- Responsive Employee Table --}}
            <div class="table-responsive border rounded-3">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-secondary small text-nowrap">#</th>
                            <th scope="col" class="text-secondary small text-nowrap">Employee Code</th>
                            <th scope="col" class="text-secondary small">Name</th>
                            <th scope="col" class="text-secondary small">Email Address</th>
                            <th scope="col" class="text-secondary small">Designation</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($department->employees as $employee)
                            <tr>
                                <td class="text-secondary">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="text-nowrap">
                                    <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                                        {{ $employee->employee_code ?: '—' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-light border
                                                    d-flex align-items-center justify-content-center
                                                    shrink-0"
                                             style="width: 36px; height: 36px;">
                                            <i class="bi bi-person text-secondary"></i>
                                        </div>

                                        <span class="fw-semibold text-dark">
                                            {{ $employee->name }}
                                        </span>
                                    </div>
                                </td>

                                <td class="text-break">
                                    {{ $employee->email }}
                                </td>

                                <td>
                                    {{ $employee->designation ?: 'Not assigned' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        @endif

    </div>

</div>
@endsection
