@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid py-3">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">My Profile</h1>
            <p class="text-secondary mb-0">
                Manage your personal information, profile photo, and password.
            </p>
        </div>
    </div>

    {{-- Password Change Validation --}}
    @if ($errors->passwordChange->any())
        <div class="alert alert-danger" role="alert">
            <strong>{{ $errors->passwordChange->first() }}</strong>
        </div>
    @endif

    {{-- Profile Validation Errors --}}
    @if ($errors->default->any())
        <div class="alert alert-danger" role="alert">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->default->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    {{-- General Error --}}
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- Profile Information Card --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">

            {{-- Last Updated Badge: Top Right --}}
            <div class="d-flex justify-content-end mb-3">
                <span class="badge rounded-pill bg-light text-secondary border px-3 py-2">
                    <i class="bi bi-clock-history me-1"></i>
                    Last Updated:
                    {{ $user->updated_at?->format('M d, Y h:i A') ?? '—' }}
                </span>
            </div>

            {{-- User Identity --}}
            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3 mb-4">

                @php
                    $profilePhotoUrl = $user->employee
                        ? $user->employee->getProfilePhotoUrl()
                        : $user->getProfilePhotoUrl();
                @endphp

                @if ($profilePhotoUrl)
                    <img
                        src="{{ $profilePhotoUrl }}"
                        alt="Your profile photo"
                        class="rounded-circle border"
                        style="width: 76px; height: 76px; object-fit: cover;"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >
                    <div
                        class="rounded-circle bg-primary-subtle text-primary align-items-center justify-content-center fw-bold"
                        style="width: 76px; height: 76px; min-width: 76px; font-size: 1.8rem; display: none;"
                        aria-label="Profile photo unavailable"
                    >
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @else
                    <div
                        class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold"
                        style="width: 76px; height: 76px; min-width: 76px; font-size: 1.8rem;"
                        aria-label="No profile photo uploaded"
                    >
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif

                <div class="min-w-0">
                    <h2 class="h5 fw-bold text-dark mb-1">
                        {{ $user->name }}
                    </h2>

                    <p class="text-secondary mb-2">
                        {{ $user->email }}
                    </p>

                    <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ ucfirst($user->role->value) }}
                    </span>
                </div>
            </div>

            <hr class="text-secondary opacity-25">

            {{-- Edit Profile Form --}}
            <h3 class="h6 fw-bold text-dark mb-3">Personal Information</h3>

            <form
                method="POST"
                action="{{ route('employee.profile.update') }}"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PUT')

                <div class="row g-4">

                    <div class="col-12">
                        <label for="profile_photo" class="form-label text-secondary small mb-1">
                            Profile Photo
                        </label>

                        <input
                            id="profile_photo"
                            type="file"
                            name="profile_photo"
                            accept=".jpg,.jpeg,.png,.webp"
                            class="form-control"
                        >

                        <div class="form-text">
                            JPG, PNG or WebP. Maximum size: 2 MB.
                        </div>

                        @error('profile_photo')
                            <p class="text-danger small mt-1 mb-0">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label text-secondary small mb-1">
                            Full Name
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            maxlength="255"
                            autocomplete="name"
                            class="form-control"
                            required
                        >

                        @error('name')
                            <p class="text-danger small mt-1 mb-0">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label text-secondary small mb-1">
                            Email Address
                        </label>

                        <input
                            id="email"
                            type="email"
                            value="{{ $user->email }}"
                            class="form-control bg-light"
                            disabled
                        >

                        <div class="form-text">
                            Email address cannot be changed here.
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="phone" class="form-label text-secondary small mb-1">
                            Phone Number
                        </label>

                        <input
                            id="phone"
                            type="tel"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            maxlength="30"
                            autocomplete="tel"
                            class="form-control"
                        >

                        @error('phone')
                            <p class="text-danger small mt-1 mb-0">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label text-secondary small mb-1">
                            Address
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            rows="4"
                            maxlength="2000"
                            autocomplete="street-address"
                            class="form-control"
                        >{{ old('address', $user->address) }}</textarea>

                        @error('address')
                            <p class="text-danger small mt-1 mb-0">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div class="border-top mt-4 pt-4">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-save me-1"></i>
                        Save Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- Change Password Card --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">

            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-shield-lock text-primary fs-5"></i>
                <h2 class="h5 fw-bold text-dark mb-0">Change Password</h2>
            </div>

            <p class="text-secondary mb-4">
                Confirm your current password and choose a new password of at least 12 characters.
            </p>

            <form method="POST" action="{{ route('employee.profile.password') }}">
                @csrf
                @method('PUT')

                <div class="row g-4">

                    <div class="col-12">
                        <label for="current_password" class="form-label text-secondary small mb-1">
                            Current Password
                        </label>

                        <input
                            id="current_password"
                            type="password"
                            name="current_password"
                            class="form-control"
                            autocomplete="current-password"
                            required
                        >

                        @error('current_password', 'passwordChange')
                            <p class="text-danger small mt-1 mb-0">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="password" class="form-label text-secondary small mb-1">
                            New Password
                        </label>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            minlength="12"
                            maxlength="128"
                            class="form-control"
                            autocomplete="new-password"
                            required
                        >

                        @error('password', 'passwordChange')
                            <p class="text-danger small mt-1 mb-0">{{ $message }}</p>
                        @enderror

                        <div class="form-text">
                            Use at least 12 characters.
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="password_confirmation" class="form-label text-secondary small mb-1">
                            Confirm New Password
                        </label>

                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            minlength="12"
                            maxlength="128"
                            class="form-control"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                </div>

                <div class="border-top mt-4 pt-4">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-key me-1"></i>
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Account Actions Card --}}
    <div class="card border-top-0 border-end-0 border-bottom-0 shadow-sm rounded-3 mb-4 border-start border-4 border-danger">
        <div class="card-body p-4">

            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-box-arrow-right text-danger fs-5"></i>
                <h2 class="h5 fw-bold text-dark mb-0">Account Actions</h2>
            </div>

            <p class="text-secondary mb-3">
                Sign out of your Employee Management System account on this device.
            </p>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>

</div>
@endsection