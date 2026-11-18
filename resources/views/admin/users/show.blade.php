@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container-fluid py-3">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                User Details
            </h1>
            <p class="text-secondary mb-0">
                View user account information and access role.
            </p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">

            <div class="d-flex flex-column align-items-start align-items-sm-end gap-2 mb-3">
                <span class="badge rounded-pill bg-light text-secondary border px-3 py-2">
                    <i class="bi bi-clock-history me-1"></i>
                    Last Updated: {{ $user->updated_at?->format('M d, Y h:i A') ?? '—' }}
                </span>
            </div>

            {{-- User Identity --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                @php
                    $userEmployee = $user->employeeIncludingTrashed;

                    $photoUrl = $userEmployee
                        ? $userEmployee->getProfilePhotoUrl()
                        : $user->getProfilePhotoUrl();
                @endphp

                @if ($photoUrl)
                    <img
                        src="{{ $photoUrl }}"
                        alt="{{ $user->name }} profile photo"
                        class="rounded-circle border"
                        style="width: 76px; height: 76px; min-width: 76px; object-fit: cover;"
                    >
                @else
                    <div
                        class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold"
                        style="width: 76px; height: 76px; min-width: 76px; font-size: 1.8rem;"
                    >
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif

                <div class="min-w-0">
                    <h3 class="h5 fw-bold text-dark mb-1">{{ $user->name }}</h3>
                    <p class="text-secondary mb-2">{{ $user->email }}</p>

                    @if ($user->role === \App\Enums\Role::Admin)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                            <i class="bi bi-shield-check me-1"></i>Admin
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border">
                            <i class="bi bi-person me-1"></i>Employee
                        </span>
                    @endif
                </div>
            </div>

            <hr class="text-secondary opacity-25">

            {{-- Details Grid --}}
            <div class="row g-4">

                <div class="col-12 col-md-6">
                    <label class="form-label text-secondary small mb-1">
                        <i class="bi bi-hash me-1"></i>User ID
                    </label>
                    <p class="fw-semibold mb-0">#{{ $user->id }}</p>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label text-secondary small mb-1">
                        <i class="bi bi-person me-1"></i>Full Name
                    </label>
                    <p class="fw-semibold mb-0">{{ $user->name }}</p>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label text-secondary small mb-1">
                        <i class="bi bi-envelope me-1"></i>Email Address
                    </label>
                    <p class="mb-0 text-break">
                        <a href="mailto:{{ $user->email }}"
                           class="text-decoration-none">
                            {{ $user->email }}
                        </a>
                    </p>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label text-secondary small mb-1">
                        <i class="bi bi-shield-lock me-1"></i>User Role
                    </label>
                    <p class="fw-semibold mb-0">
                        {{ ucfirst($user->role->value) }}
                    </p>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label text-secondary small mb-1">
                        <i class="bi bi-calendar-plus me-1"></i>Created At
                    </label>
                    <p class="fw-medium mb-0">
                        {{ $user->created_at?->format('d M Y, h:i A') ?? '—' }}
                    </p>
                </div>

            </div>

            {{-- Footer Actions --}}
            <div class="d-flex flex-column flex-sm-row justify-content-start gap-2 border-top mt-4 pt-4">

                <a href="{{ route('admin.users.index') }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to Users
                </a>

                <a href="{{ route('admin.users.edit', $user) }}"
                   class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil-square me-1"></i> Edit User
                </a>

            </div>

        </div>
    </div>

</div>
@endsection
