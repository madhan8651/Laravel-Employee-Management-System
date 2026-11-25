@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="container-fluid py-3 employee-page-container">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Employees</h1>
            <p class="text-secondary mb-0">Manage your employee records.</p>
        </div>

    </div>
    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Employee Directory Card --}}
    <div class="card border-0 shadow-sm rounded-3 employee-directory-card">

        <div class="card-body pb-0 d-flex justify-content-end">
            <a href="{{ route('admin.employees.create') }}"
               class="btn btn-primary d-inline-flex align-items-center justify-content-center text-nowrap">
                <i class="bi bi-person-plus me-2"></i> Add Employee
            </a>
        </div>

        {{-- Employee Filters --}}
        <div class="card-body employee-filter-panel">
            <form id="employee-filter-form"
                method="GET"
                action="{{ route('admin.employees.index') }}">
                <div class="row g-3">

                    {{-- Search --}}
                    <div class="col-12 col-md-6 col-xl-4">
                        <label for="search" class="form-label">Search Employees</label>
                        <input type="search"
                               name="search"
                               id="search"
                               class="form-control"
                               placeholder="Name, email or employee code"
                               value="{{ $filters['search'] ?? '' }}">
                    </div>

                    {{-- Department --}}
                    <div class="col-12 col-md-6 col-xl-4">
                        <label for="department_id" class="form-label">Department</label>
                        <select name="department_id"
                                id="department_id"
                                class="form-select">
                            <option value="">All Departments</option>

                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    @selected((string) ($filters['department_id'] ?? '') === (string) $department->id)>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="col-12 col-md-6 col-xl-4">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value=""
                                @selected(($filters['status'] ?? '') === '')>
                                All Statuses
                            </option>

                            <option value="active"
                                @selected(($filters['status'] ?? '') === 'active')>
                                Active
                            </option>

                            <option value="inactive"
                                @selected(($filters['status'] ?? '') === 'inactive')>
                                Inactive
                            </option>

                            <option value="terminated"
                                @selected(($filters['status'] ?? '') === 'terminated')>
                                Terminated
                            </option>
                        </select>
                    </div>

                    {{-- Gender --}}
                    <div class="col-12 col-md-6 col-xl-4">
                        <label for="gender" class="form-label">Gender</label>
                        <select name="gender" id="gender" class="form-select">
                            <option value=""
                                @selected(($filters['gender'] ?? '') === '')>
                                All Genders
                            </option>

                            <option value="male"
                                @selected(($filters['gender'] ?? '') === 'male')>
                                Male
                            </option>

                            <option value="female"
                                @selected(($filters['gender'] ?? '') === 'female')>
                                Female
                            </option>

                            <option value="other"
                                @selected(($filters['gender'] ?? '') === 'other')>
                                Other
                            </option>
                        </select>
                    </div>

                    {{-- Sort By --}}
                    <div class="col-12 col-md-6 col-xl-4">
                        <label for="sort" class="form-label">Sort By</label>
                        <select name="sort" id="sort" class="form-select">
                            <option value="created_at"
                                @selected(($sortBy ?? 'name') === 'created_at')>
                                Date Created
                            </option>

                            <option value="name"
                                @selected(($sortBy ?? 'name') === 'name')>
                                Name
                            </option>

                            <option value="employee_code"
                                @selected(($sortBy ?? 'name') === 'employee_code')>
                                Employee Code
                            </option>

                            <option value="email"
                                @selected(($sortBy ?? 'name') === 'email')>
                                Email
                            </option>

                            <option value="date_of_joining"
                                @selected(($sortBy ?? 'name') === 'date_of_joining')>
                                Joining Date
                            </option>
                        </select>
                    </div>

                    {{-- Sort Direction --}}
                    <div class="col-12 col-md-6 col-xl-4">
                        <label for="direction" class="form-label">Order</label>
                        <select name="direction" id="direction" class="form-select">
                            <option value="asc"
                                @selected(($direction ?? 'asc') === 'asc')>
                                Ascending
                            </option>

                            <option value="desc"
                                @selected(($direction ?? 'asc') === 'desc')>
                                Descending
                            </option>
                        </select>
                    </div>

                    {{-- Filter Actions --}}
                    <div class="col-12 d-flex flex-wrap gap-2 employee-filter-actions">
                        <a href="{{ route('admin.employees.index', ['reset' => 1]) }}"
                           class="btn btn-primary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>

        {{-- Employee Results --}}
        <div id="employee-empty-state"
             class="card-body text-center py-5"
             @if (!$employees->isEmpty()) style="display: none;" @endif>

            <i class="bi bi-people display-5 text-secondary"></i>

            <h2 class="h5 fw-bold mt-3">No employees found</h2>

            <p class="text-secondary mb-3">
                No employees match your current filters.
            </p>

            <a href="{{ route('admin.employees.index', ['reset' => 1]) }}"
               class="btn btn-outline-primary btn-sm"
               id="clear-employee-filters">
                Clear Filters
            </a>
        </div>

        <div id="employee-loading"
             class="text-center py-4"
             style="display: none;">

            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>

            <p class="text-secondary mt-2 mb-0">
                Loading employees...
            </p>
        </div>

        <div id="employee-table-container"
             data-view-url="{{ route('admin.employees.show', ['employee' => '__ID__']) }}"
             data-edit-url="{{ route('admin.employees.edit', ['employee' => '__ID__']) }}"
             data-delete-url="{{ route('admin.employees.destroy', ['employee' => '__ID__']) }}"
             data-toggle-url="{{ route('admin.employees.toggle-status', ['employee' => '__ID__']) }}"
             @if ($employees->isEmpty()) style="display: none;" @endif>

            {{-- Employee Table --}}
            <div class="table-responsive employee-table-wrapper">
                <table class="table table-hover align-middle mb-0 employee-table">
                    <thead>
                        <tr>
                            <th scope="col" class="px-3">#</th>
                            <th scope="col">Employee Code</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Designation</th>
                            <th scope="col">Department</th>
                            <th scope="col">Gender</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="employee-table-body">
                        @foreach ($employees as $employee)
                            @php($employeeStatus = $employee->status?->value)
                            @php($employeeGender = $employee->gender?->value)

                            <tr>
                                <td class="px-3 text-secondary">
                                    {{ $employees->firstItem() + $loop->index }}
                                </td>

                                <td class="text-nowrap">
                                    <span class="badge bg-light text-dark border">
                                        {{ $employee->employee_code }}
                                    </span>
                                </td>

                                <td class="text-nowrap">
                                    <span class="fw-semibold text-dark">
                                        {{ $employee->name }}
                                    </span>
                                </td>

                                <td>
                                    <a href="mailto:{{ $employee->email }}"
                                       class="text-decoration-none">
                                        {{ $employee->email }}
                                    </a>
                                </td>

                                <td>{{ $employee->designation ?: '—' }}</td>

                                {{-- Department --}}
                                <td>
                                    @if ($employee->department)
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ $employee->department->name }}
                                        </span>
                                    @else
                                        <span class="text-secondary">Unassigned</span>
                                    @endif
                                </td>

                                {{-- Gender --}}
                                <td class="text-nowrap">
                                    @if ($employeeGender === 'male')
                                        Male
                                    @elseif ($employeeGender === 'female')
                                        Female
                                    @elseif ($employeeGender === 'other')
                                        Other
                                    @else
                                        <span class="text-secondary">Not specified</span>
                                    @endif
                                </td>

                                {{-- Employment Status --}}
                                <td class="text-nowrap">
                                    @if ($employeeStatus === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif ($employeeStatus === 'inactive')
                                        <span class="badge bg-secondary">Inactive</span>
                                    @elseif ($employeeStatus === 'terminated')
                                        <span class="badge bg-danger">Terminated</span>
                                    @else
                                        <span class="badge bg-light text-dark">Not set</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="text-nowrap pe-3">
                                    <div class="department-actions">

                                        @can('view', $employee)
                                            <a href="{{ route('admin.employees.show', $employee) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="View Employee"
                                               aria-label="View Employee">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" focusable="false"><path d="M8 3.5c-3.5 0-6.3 3.4-7.3 4.5 1 1.1 3.8 4.5 7.3 4.5s6.3-3.4 7.3-4.5C14.3 6.9 11.5 3.5 8 3.5Zm0 7.5a3 3 0 1 1 0-6 3 3 0 0 1 0 6Zm0-1.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/></svg>
                                            </a>
                                        @endcan

                                        @can('update', $employee)
                                            <a href="{{ route('admin.employees.edit', $employee) }}"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Edit Employee"
                                               aria-label="Edit Employee">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" focusable="false"><path d="M13.5 1a1.5 1.5 0 0 1 1.06 2.56l-8.8 8.8-3.4.85.85-3.4 8.8-8.8A1.5 1.5 0 0 1 13.5 1ZM3.2 10.4l-.4 1.6 1.6-.4 7.9-7.9-1.2-1.2-7.9 7.9Z"/><path d="M2 2h7v1.5H3.5v9h9V8H14v5a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1Z"/></svg>
                                            </a>
                                        @endcan

                                        @can('update', $employee)
                                            @if ($employeeStatus !== 'terminated')
                                                <form action="{{ route('admin.employees.toggle-status', $employee) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to {{ $employeeStatus === 'active' ? 'deactivate' : 'activate' }} this employee?')">
                                                    @csrf
                                                    @method('PATCH')

                                                    @if ($employeeStatus === 'active')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-warning"
                                                                title="Deactivate employee"
                                                                aria-label="Deactivate employee">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" focusable="false"><path d="M5 3h6a5 5 0 0 1 0 10H5A5 5 0 0 1 5 3Zm0 1.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z"/><circle cx="11" cy="8" r="2.5"/></svg>
                                                        </button>
                                                    @else
                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-success"
                                                                title="Activate employee"
                                                                aria-label="Activate employee">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true" focusable="false"><path d="M5 3h6a5 5 0 0 1 0 10H5A5 5 0 0 1 5 3Zm0 1.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z"/><circle cx="5" cy="8" r="2.5"/></svg>
                                                        </button>
                                                    @endif
                                                </form>
                                            @endif
                                        @endcan

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- AJAX Pagination --}}
        <style>
            .employee-table .department-actions .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 32px;
                min-height: 32px;
            }

            .employee-table .department-actions .btn svg {
                display: block;
                flex: 0 0 16px;
                width: 16px;
                height: 16px;
                fill: currentColor;
            }

            #employee-pagination .page-link {
                color: var(--bs-primary);
                border-color: var(--bs-primary);
            }

            #employee-pagination .page-item.active .page-link {
                color: var(--bs-btn-color, #fff);
                background-color: var(--bs-primary);
                border-color: var(--bs-primary);
            }
        </style>
        <div id="employee-pagination" class="card-footer bg-white border-0 py-3">
            @if ($employees->hasPages())
                {{ $employees->links('pagination::bootstrap-5') }}
            @endif
        </div>
    </div>
</div>
@push('scripts')
    <script src="{{ asset('js/employee-filters.js') }}" defer></script>
@endpush
@endsection
