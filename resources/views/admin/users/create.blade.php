@extends('layouts.app')

@section('title', 'Add User')

@section('content')
<div class="container-fluid px-0">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title">Add User</h1>
            <p class="page-subtitle mb-0">
                Create a new user account for the Employee Management System.
            </p>
        </div>

        <a href="{{ route('admin.users.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Users
        </a>
    </div>


    {{-- User Form --}}
    <div class="form-card">

        {{-- Form Heading --}}
        <div class="mb-4 pb-3 border-bottom">
            <h2 class="h5 fw-bold mb-1">
                <i class="bi bi-person-plus me-2 text-primary"></i>
                User Information
            </h2>

            <p class="text-secondary mb-0">
                Enter the user's details. They will receive an email to set their own password.
            </p>
        </div>

        {{-- Create User Form --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Please correct the following errors:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    placeholder="Enter full name"
                    required
                >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    placeholder="Enter email address"
                    required
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">User Role <span class="text-danger">*</span></label>
                <select
                    name="role"
                    id="role"
                    class="form-select @error('role') is-invalid @enderror"
                    required
                >
                    <option value="">Select Role</option>
                    <option value="employee" {{ old('role') === 'employee' ? 'selected' : '' }}>
                        Employee
                    </option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                        Admin
                    </option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Form Actions --}}
            <div class="d-flex flex-wrap gap-2 mt-4 pt-4 border-top">

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-check me-1"></i>
                    Create User
                </button>

                <a href="{{ route('admin.users.index') }}"
                   class="btn btn-light border">
                    Cancel
                </a>

            </div>
        </form>
    </div>

</div>
@endsection
