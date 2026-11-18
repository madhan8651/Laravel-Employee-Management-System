@extends('layouts.app')

@section('title', 'Add Employee')

@section('content')
<div class="container-fluid px-0">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title">Add Employee</h1>
            <p class="page-subtitle mb-0">
                Add a new employee to your organization.
            </p>
        </div>

        <a href="{{ route('admin.employees.index') }}"
           class="btn btn-outline-secondary">
            &larr; Back to Employees
        </a>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation Error Summary --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Employee Form --}}
    <div class="form-card">
        <div class="mb-4 pb-3 border-bottom">
            <h2 class="h5 fw-bold mb-1">
                <span aria-hidden="true">&#128100;</span>
                Employee Information
            </h2>
            <p class="text-secondary mb-0">
                Enter the employee's details below.
                Fields marked <span class="text-danger">*</span> are required.
            </p>
        </div>

        <form method="POST"
              action="{{ route('admin.employees.store') }}"
              enctype="multipart/form-data"
              novalidate>
            @csrf

            <div class="row g-4">

                {{-- Employee Code --}}
                <div class="col-md-6">
                    <label for="employee_code" class="form-label">
                        Employee Code <span class="text-danger">*</span>
                    </label>

                    <input
                        id="employee_code"
                        type="text"
                        name="employee_code"
                        class="form-control @error('employee_code') is-invalid @enderror"
                        value="{{ old('employee_code') }}"
                        placeholder="e.g. EMP001"
                        maxlength="50"
                        autocomplete="off"
                        required
                    >

                    @error('employee_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Full Name --}}
                <div class="col-md-6">
                    <label for="name" class="form-label">
                        Full Name <span class="text-danger">*</span>
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="Enter full name"
                        maxlength="255"
                        autocomplete="name"
                        required
                    >

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label for="email" class="form-label">
                        Email Address <span class="text-danger">*</span>
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="name@example.com"
                        maxlength="255"
                        autocomplete="email"
                        required
                    >

                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Designation --}}
                <div class="col-md-6">
                    <label for="designation" class="form-label">
                        Designation <span class="text-danger">*</span>
                    </label>

                    <input
                        id="designation"
                        type="text"
                        name="designation"
                        class="form-control @error('designation') is-invalid @enderror"
                        value="{{ old('designation') }}"
                        placeholder="e.g. Software Engineer"
                        maxlength="255"
                        required
                    >

                    @error('designation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="col-md-6">
                    <label for="phone" class="form-label">
                        Phone Number <span class="text-danger">*</span>
                    </label>

                    <input
                        id="phone"
                        type="tel"
                        name="phone"
                        class="form-control @error('phone') is-invalid @enderror"
                        value="{{ old('phone') }}"
                        placeholder="Enter phone number"
                        maxlength="20"
                        autocomplete="tel"
                        required
                    >

                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Department --}}
                <div class="col-md-6">
                    <label for="department_id" class="form-label">
                        Department
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        id="department_id"
                        name="department_id"
                        class="form-select @error('department_id') is-invalid @enderror"
                    >
                        <option value="">Select Department</option>

                        @foreach ($departments as $department)
                            <option
                                value="{{ $department->id }}"
                                @selected((string) old('department_id') === (string) $department->id)
                            >
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div class="form-text">
                        Active employees must have a department.
                    </div>
                </div>

                {{-- Gender --}}
                <div class="col-md-6">
                    <label for="gender" class="form-label">
                        Gender <span class="text-danger">*</span>
                    </label>

                    <select
                        id="gender"
                        name="gender"
                        class="form-select @error('gender') is-invalid @enderror"
                        required
                    >
                        <option value="">Select Gender</option>
                        <option value="male" @selected(old('gender') === 'male')>
                            Male
                        </option>
                        <option value="female" @selected(old('gender') === 'female')>
                            Female
                        </option>
                    </select>

                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Employment Status --}}
                <div class="col-md-6">
                    <label for="status" class="form-label">
                        Employment Status <span class="text-danger">*</span>
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="form-select @error('status') is-invalid @enderror"
                        required
                    >
                        <option value="active"
                            @selected(old('status', 'active') === 'active')>
                            Active
                        </option>

                        <option value="inactive"
                            @selected(old('status') === 'inactive')>
                            Inactive
                        </option>

                        <option value="terminated"
                            @selected(old('status') === 'terminated')>
                            Terminated
                        </option>
                    </select>

                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Date of Birth --}}
                <div class="col-md-6">
                    <label for="date_of_birth" class="form-label">
                        Date of Birth <span class="text-danger">*</span>
                    </label>

                    <input
                        id="date_of_birth"
                        type="date"
                        name="date_of_birth"
                        class="form-control @error('date_of_birth') is-invalid @enderror"
                        value="{{ old('date_of_birth') }}"
                        max="{{ now()->subYears(18)->toDateString() }}"
                        required
                    >

                    @error('date_of_birth')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div class="form-text">
                        Employee must be at least 18 years old.
                    </div>
                </div>

                {{-- Date of Joining --}}
                <div class="col-md-6">
                    <label for="date_of_joining" class="form-label">
                        Date of Joining <span class="text-danger">*</span>
                    </label>

                    <input
                        id="date_of_joining"
                        type="date"
                        name="date_of_joining"
                        class="form-control @error('date_of_joining') is-invalid @enderror"
                        value="{{ old('date_of_joining') }}"
                        required
                    >

                    @error('date_of_joining')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Salary --}}
                <div class="col-md-6">
                    <label for="salary" class="form-label">
                        Salary <span class="text-danger">*</span>
                    </label>

                    <input
                        id="salary"
                        type="number"
                        name="salary"
                        class="form-control @error('salary') is-invalid @enderror"
                        value="{{ old('salary') }}"
                        placeholder="Enter salary"
                        min="0"
                        max="9999999999.99"
                        step="0.01"
                        required
                    >

                    @error('salary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Profile Photo --}}
                <div class="col-md-6">
                    <label for="profile_photo" class="form-label">
                        Profile Photo
                    </label>

                    <input
                        id="profile_photo"
                        type="file"
                        name="profile_photo"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="form-control @error('profile_photo') is-invalid @enderror"
                        aria-describedby="profile-photo-help"
                    >

                    <div id="profile-photo-help" class="form-text">
                        JPG, JPEG, PNG, or WebP. Maximum file size: 2 MB
                        and dimensions up to 3000 × 3000 pixels.
                    </div>

                    @error('profile_photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Address --}}
                <div class="col-12">
                    <label for="address" class="form-label">
                        Address <span class="text-danger">*</span>
                    </label>

                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                        maxlength="5000"
                        class="form-control @error('address') is-invalid @enderror"
                        placeholder="Enter address"
                        required
                    >{{ old('address') }}</textarea>

                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input
                            type="checkbox"
                            name="create_user_account"
                            id="create_user_account"
                            value="1"
                            class="form-check-input"
                            @checked(old('create_user_account'))
                        >

                        <label
                            class="form-check-label"
                            for="create_user_account"
                        >
                            Create employee login account
                        </label>

                        <div class="form-text">
                            Creates a login account using this employee's name and email
                            and sends password setup instructions.
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <label for="role" class="form-label">Login Role</label>

                    <select
                        name="role"
                        id="role"
                        class="form-select"
                        required
                    >
                        <option value="employee" @selected(old('role', 'employee') === 'employee')>
                            Employee
                        </option>
                        <option value="admin" @selected(old('role') === 'admin')>
                            Admin
                        </option>
                    </select>

                    @error('role')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror

                    <div class="form-text">
                        Employee is selected by default. Choose Admin only when administrative access is required.
                    </div>
                </div>

            </div>

            {{-- Form Actions --}}
            <div class="d-flex flex-wrap gap-2 mt-4 pt-4 border-top">
                <button type="submit" class="btn btn-primary">
                    + Create Employee
                </button>

                <a href="{{ route('admin.employees.index') }}"
                   class="btn btn-light border">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection