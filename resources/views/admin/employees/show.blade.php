
@extends('layouts.app')

@section('title', 'Employee Details')

@section('content')
<div class="container-fluid px-0">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title">Employee Details</h1>
            <p class="page-subtitle mb-0">
                View employee information and track changes to their records.
            </p>
        </div>
    </div>

    {{-- Employee Information --}}
    <div class="form-card mb-4 position-relative">

        {{-- Last Updated: Top-Right Corner --}}
        <div class="position-absolute top-0 end-0 p-3">
            <span class="badge rounded-pill bg-light text-secondary border px-3 py-2">
                <i class="bi bi-clock-history me-1"></i>
                Last Updated: {{ $employee->updated_at?->format('M d, Y h:i A') ?? 'Not available' }}
            </span>
        </div>

        {{-- Employee Profile Header --}}
        <div class="d-flex flex-wrap align-items-center gap-3 mb-4 pb-4 border-bottom pe-3">

            {{-- Profile Photo --}}
            <div class="flex-shrink-0">
                @if ($employee->profile_photo)
                    <img
                        src="{{ $employee->getProfilePhotoUrl() }}"
                        alt="{{ $employee->name }} profile photo"
                        class="rounded-circle border"
                        style="width: 76px; height: 76px; object-fit: cover;"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >

                    <div
                        class="rounded-circle bg-primary-subtle text-primary align-items-center justify-content-center fw-bold"
                        style="width: 76px; height: 76px; font-size: 28px; display: none;">
                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                    </div>
                @else
                    <div
                        class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold"
                        style="width: 76px; height: 76px; font-size: 28px;">
                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            {{-- Employee Name and Designation --}}
            <div class="flex-grow-1">
                <h2 class="h4 fw-bold mb-1">
                    {{ $employee->name }}
                </h2>

                <p class="text-secondary mb-2">
                    {{ $employee->designation ?: 'No designation assigned' }}
                </p>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                        <i class="bi bi-person-badge me-1"></i>
                        {{ $employee->employee_code ?: 'No employee code' }}
                    </span>

                    @if ($employee->department)
                        <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                            <i class="bi bi-building me-1"></i>
                            {{ $employee->department->name }}
                        </span>
                    @endif
                </div>
            </div>

        </div>

        {{-- Details Heading --}}
        <div class="mb-4">
            <h3 class="h5 fw-bold mb-1">Employee Information</h3>
            <p class="text-secondary mb-0">
                Personal details, employment information, and contact details.
            </p>
        </div>

        {{-- Employee Details Grid --}}
        <div class="row g-4">

            {{-- Employee Code --}}
            <div class="col-12 col-md-6">
                <div class="border-bottom pb-3 h-100">
                    <div class="small fw-semibold text-secondary mb-2">
                        <i class="bi bi-hash me-1"></i>
                        Employee Code
                    </div>
                    <div class="fw-medium text-break">
                        {{ $employee->employee_code ?: 'Not provided' }}
                    </div>
                </div>
            </div>

            {{-- Full Name --}}
            <div class="col-12 col-md-6">
                <div class="border-bottom pb-3 h-100">
                    <div class="small fw-semibold text-secondary mb-2">
                        <i class="bi bi-person me-1"></i>
                        Full Name
                    </div>
                    <div class="fw-medium text-break">
                        {{ $employee->name }}
                    </div>
                </div>
            </div>

            {{-- Email --}}
            <div class="col-12 col-md-6">
                <div class="border-bottom pb-3 h-100">
                    <div class="small fw-semibold text-secondary mb-2">
                        <i class="bi bi-envelope me-1"></i>
                        Email Address
                    </div>
                    <div class="text-break">
                        {{ $employee->email }}
                    </div>
                </div>
            </div>

            {{-- Phone --}}
            <div class="col-12 col-md-6">
                <div class="border-bottom pb-3 h-100">
                    <div class="small fw-semibold text-secondary mb-2">
                        <i class="bi bi-telephone me-1"></i>
                        Phone Number
                    </div>
                    <div>
                        {{ $employee->phone ?: 'Not provided' }}
                    </div>
                </div>
            </div>

            {{-- Designation --}}
            <div class="col-12 col-md-6">
                <div class="border-bottom pb-3 h-100">
                    <div class="small fw-semibold text-secondary mb-2">
                        <i class="bi bi-briefcase me-1"></i>
                        Designation
                    </div>
                    <div>
                        {{ $employee->designation ?: 'Not assigned' }}
                    </div>
                </div>
            </div>

            {{-- Department --}}
            <div class="col-12 col-md-6">
                <div class="border-bottom pb-3 h-100">
                    <div class="small fw-semibold text-secondary mb-2">
                        <i class="bi bi-building me-1"></i>
                        Department
                    </div>
                    <div>
                        @if ($employee->department)
                            <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                                {{ $employee->department->name }}
                            </span>
                        @else
                            <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">
                                Unassigned
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Gender --}}
            <div class="col-12 col-md-6">
                <div class="border-bottom pb-3 h-100">
                    <div class="small fw-semibold text-secondary mb-2">
                        <i class="bi bi-person-lines-fill me-1"></i>
                        Gender
                    </div>
                    <div>
                        {{ $employee->gender?->value
                            ? ucfirst($employee->gender->value)
                            : 'Not provided' }}
                    </div>
                </div>
            </div>

            {{-- Employment Status --}}
            <div class="col-12 col-md-6">
                <div class="border-bottom pb-3 h-100">
                    <div class="small fw-semibold text-secondary mb-2">
                        <i class="bi bi-person-check me-1"></i>
                        Employment Status
                    </div>
                    <div>
                        @php
                            $statusValue = $employee->status?->value;
                        @endphp

                        @if ($statusValue)
                            <span class="badge rounded-pill
                                {{ strtolower($statusValue) === 'active'
                                    ? 'bg-success-subtle text-success'
                                    : 'bg-secondary-subtle text-secondary' }} px-3 py-2">
                                {{ ucfirst(str_replace('_', ' ', $statusValue)) }}
                            </span>
                        @else
                            <span class="text-secondary">Not provided</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Date of Birth --}}
            <div class="col-12 col-md-6">
                <div class="border-bottom pb-3 h-100">
                    <div class="small fw-semibold text-secondary mb-2">
                        <i class="bi bi-calendar-event me-1"></i>
                        Date of Birth
                    </div>
                    <div>
                        {{ $employee->date_of_birth?->format('d M Y') ?? 'Not provided' }}
                    </div>
                </div>
            </div>

            {{-- Date of Joining --}}
            <div class="col-12 col-md-6">
                <div class="border-bottom pb-3 h-100">
                    <div class="small fw-semibold text-secondary mb-2">
                        <i class="bi bi-calendar-check me-1"></i>
                        Date of Joining
                    </div>
                    <div>
                        {{ $employee->date_of_joining?->format('d M Y') ?? 'Not provided' }}
                    </div>
                </div>
            </div>

            {{-- Salary --}}
            <div class="col-12 col-md-6">
                <div class="border-bottom pb-3 h-100">
                    <div class="small fw-semibold text-secondary mb-2">
                        <i class="bi bi-cash-coin me-1"></i>
                        Salary
                    </div>
                    <div class="fw-semibold">
                        {{ $employee->salary !== null
                            ? number_format((float) $employee->salary, 2)
                            : 'Not provided' }}
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div class="col-12">
                <div class="border-bottom pb-3">
                    <div class="small fw-semibold text-secondary mb-2">
                        <i class="bi bi-geo-alt me-1"></i>
                        Address
                    </div>
                    <div class="text-break">
                        {{ $employee->address ?: 'Not provided' }}
                    </div>
                </div>
            </div>

        </div>

        {{-- Employee Actions --}}
        <div class="d-flex flex-wrap gap-2 mt-4 pt-4 border-top">
            <a href="{{ route('admin.employees.edit', $employee) }}"
               class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i>
                Edit Employee
            </a>

            <a href="{{ route('admin.employees.index') }}"
               class="btn btn-light border">
                Back to Employees
            </a>
        </div>

    </div>

    {{-- Activity History --}}
    <div class="form-card">

        {{-- Activity Header --}}
        <div class="mb-4 pb-3 border-bottom">
            <h2 class="h5 fw-bold mb-1">
                <i class="bi bi-clock-history me-2 text-primary"></i>
                Activity History
            </h2>
            <p class="text-secondary mb-0">
                Review changes and actions recorded for this employee.
            </p>
        </div>

        @forelse ($activityLogs as $log)

            @php
                $activityIcon = match ($log->action) {
                    'created' => 'bi-person-plus',
                    'updated' => 'bi-pencil-square',
                    'deleted' => 'bi-trash',
                    'restored' => 'bi-arrow-counterclockwise',
                    'force_deleted' => 'bi-trash-fill',
                    default => 'bi-clock-history',
                };

                $activityColor = match ($log->action) {
                    'created', 'restored' => 'text-success bg-success-subtle',
                    'updated' => 'text-primary bg-primary-subtle',
                    'deleted', 'force_deleted' => 'text-danger bg-danger-subtle',
                    default => 'text-secondary bg-secondary-subtle',
                };

                $oldValues = $log->old_values ?? [];
                $newValues = $log->new_values ?? [];

                $hiddenFields = [
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'password',
                    'remember_token',
                ];

                $changedFields = array_diff(
                    array_unique([
                        ...array_keys($oldValues),
                        ...array_keys($newValues),
                    ]),
                    $hiddenFields
                );
            @endphp

            <div class="d-flex gap-3
                {{ !$loop->last ? 'pb-4 mb-4 border-bottom' : '' }}">

                {{-- Activity Icon --}}
                <div class="flex-shrink-0">
                    <div class="rounded-circle {{ $activityColor }}
                                d-flex align-items-center justify-content-center"
                         style="width: 42px; height: 42px;">
                        <i class="bi {{ $activityIcon }} fs-5"></i>
                    </div>
                </div>

                {{-- Activity Details --}}
                <div class="flex-grow-1 min-w-0">

                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                        <div>
                            <h3 class="h6 fw-bold mb-1">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </h3>

                            <p class="text-secondary mb-2">
                                {{ $log->description ?: 'Employee activity recorded.' }}
                            </p>
                        </div>

                        <span class="badge rounded-pill {{ $activityColor }} px-3 py-2">
                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                        </span>
                    </div>

                    {{-- Activity Metadata --}}
                    <div class="small text-secondary mb-3">
                        <div class="d-flex flex-wrap gap-3">
                            <span>
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $log->created_at?->format('d M Y, h:i A') ?? 'Date unavailable' }}
                            </span>

                            <span>
                                <i class="bi bi-person me-1"></i>
                                {{ $log->user?->name ?? 'System / Unknown user' }}
                            </span>
                        </div>
                    </div>

                    {{-- Changed Values --}}
                    @if (count($changedFields))
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="small fw-semibold text-dark mb-3">
                                <i class="bi bi-arrow-left-right me-1"></i>
                                Change Details
                            </div>

                            @foreach ($changedFields as $field)
                                <div class="{{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                                    <div class="small fw-semibold text-secondary mb-2">
                                        {{ ucwords(str_replace('_', ' ', $field)) }}
                                    </div>

                                    @if (array_key_exists($field, $oldValues))
                                        <div class="small mb-1">
                                            <span class="text-danger fw-semibold">Old:</span>
                                            <span class="text-break">
                                                {{ is_scalar($oldValues[$field]) || $oldValues[$field] === null
                                                    ? ($oldValues[$field] ?? 'Empty')
                                                    : json_encode($oldValues[$field]) }}
                                            </span>
                                        </div>
                                    @endif

                                    @if (array_key_exists($field, $newValues))
                                        <div class="small">
                                            <span class="text-success fw-semibold">New:</span>
                                            <span class="text-break">
                                                {{ is_scalar($newValues[$field]) || $newValues[$field] === null
                                                    ? ($newValues[$field] ?? 'Empty')
                                                    : json_encode($newValues[$field]) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>

        @empty
            <div class="text-center py-5">
                <i class="bi bi-clock-history text-secondary" style="font-size: 2.5rem;"></i>

                <h3 class="h6 fw-semibold mt-3">No Activity Yet</h3>

                <p class="text-secondary small mb-0">
                    Activity for this employee will appear here when recorded.
                </p>
            </div>
        @endforelse

    </div>

</div>
@endsection
