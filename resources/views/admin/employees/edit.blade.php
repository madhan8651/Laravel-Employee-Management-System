@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
<div class="container-fluid px-0">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title">Edit Employee</h1>
            <p class="page-subtitle mb-0">
                Update employee details, employment information, and profile photo.
            </p>
        </div>

        <a href="{{ route('admin.employees.index') }}"
           class="btn btn-outline-secondary">
            &larr; Back to Employees
        </a>
    </div>

    @include('partials.alerts')

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please correct the following errors:</strong>
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

    <div class="form-card">

        {{-- Employee Code --}}
        <div class="mb-4 pb-3 border-bottom">
            <span class="text-secondary small">Employee Code</span>
            <div class="fw-bold fs-5">{{ $employee->employee_code }}</div>
        </div>

        {{-- enctype is required for file uploads --}}
        <form method="POST"
              action="{{ route('admin.employees.update', $employee) }}"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">

                {{-- Full Name --}}
                <div class="col-md-6">
                    <label for="name" class="form-label">
                        Full Name <span class="text-danger">*</span>
                    </label>
                    <input id="name"
                           name="name"
                           type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $employee->name) }}"
                           maxlength="255"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label for="email" class="form-label">
                        Email Address <span class="text-danger">*</span>
                    </label>
                    <input id="email"
                           name="email"
                           type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $employee->email) }}"
                           maxlength="255"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Designation --}}
                <div class="col-md-6">
                    <label for="designation" class="form-label">Designation</label>
                    <input id="designation"
                           name="designation"
                           type="text"
                           class="form-control @error('designation') is-invalid @enderror"
                           value="{{ old('designation', $employee->designation) }}"
                           maxlength="255">
                    @error('designation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input id="phone"
                           name="phone"
                           type="text"
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $employee->phone) }}"
                           maxlength="20">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Department --}}
                <div class="col-md-6">
                    <label for="department_id" class="form-label">Department</label>
                    <select id="department_id"
                            name="department_id"
                            class="form-select @error('department_id') is-invalid @enderror">
                        <option value="">Unassigned</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}"
                                @selected((string) old('department_id', $employee->department_id) === (string) $department->id)>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Active employees must have a department under the current validation rule.
                    </div>
                </div>

                {{-- Gender --}}
                <div class="col-md-6">
                    <label for="gender" class="form-label">Gender</label>
                    <select id="gender"
                            name="gender"
                            class="form-select @error('gender') is-invalid @enderror">
                        <option value="">Select Gender</option>
                        <option value="male"
                            @selected(old('gender', $employee->gender?->value) === 'male')>
                            Male
                        </option>
                        <option value="female"
                            @selected(old('gender', $employee->gender?->value) === 'female')>
                            Female
                        </option>
                        <option value="other"
                            @selected(old('gender', $employee->gender?->value) === 'other')>
                            Other
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
                    <select id="status"
                            name="status"
                            class="form-select @error('status') is-invalid @enderror"
                            required>
                        <option value="active"
                            @selected(old('status', $employee->status?->value ?? 'active') === 'active')>
                            Active
                        </option>
                        <option value="inactive"
                            @selected(old('status', $employee->status?->value ?? 'active') === 'inactive')>
                            Inactive
                        </option>
                        <option value="terminated"
                            @selected(old('status', $employee->status?->value ?? 'active') === 'terminated')>
                            Terminated
                        </option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Date of Birth --}}
                <div class="col-md-6">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input id="date_of_birth"
                           name="date_of_birth"
                           type="date"
                           class="form-control @error('date_of_birth') is-invalid @enderror"
                           value="{{ old('date_of_birth', $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}"
                           max="{{ now()->subDay()->format('Y-m-d') }}">
                    @error('date_of_birth')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">The employee must be at least 18 years old.</div>
                </div>

                {{-- Date of Joining --}}
                <div class="col-md-6">
                    <label for="date_of_joining" class="form-label">Date of Joining</label>
                    <input id="date_of_joining"
                           name="date_of_joining"
                           type="date"
                           class="form-control @error('date_of_joining') is-invalid @enderror"
                           value="{{ old('date_of_joining', $employee->date_of_joining ? $employee->date_of_joining->format('Y-m-d') : '') }}">
                    @error('date_of_joining')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Salary --}}
                <div class="col-md-6">
                    <label for="salary" class="form-label">Salary</label>
                    <input id="salary"
                           name="salary"
                           type="number"
                           step="0.01"
                           min="0"
                           class="form-control @error('salary') is-invalid @enderror"
                           value="{{ old('salary', $employee->salary) }}">
                    @error('salary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Profile Photo --}}
                <div class="col-md-6">
                    <label for="profile_photo" class="form-label">Profile Photo</label>

                    @if ($employee->profile_photo)
                        <div class="mb-2">
                            <img src="{{ $employee->getProfilePhotoUrl() }}"
                                 alt="Current profile photo"
                                 class="rounded border"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                            <div class="form-text">Current profile photo</div>
                        </div>
                    @endif

                    <input id="profile_photo"
                           name="profile_photo"
                           type="file"
                           accept=".jpg,.jpeg,.png,.webp"
                           class="form-control @error('profile_photo') is-invalid @enderror">
                    @error('profile_photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        JPG, JPEG, PNG, or WebP. Maximum 2 MB and 3000 x 3000 pixels.
                        Leave empty to keep the current photo.
                    </div>
                </div>

                {{-- Address --}}
                <div class="col-12">
                    <label for="address" class="form-label">Address</label>
                    <textarea id="address"
                              name="address"
                              rows="3"
                              maxlength="1000"
                              class="form-control @error('address') is-invalid @enderror">{{ old('address', $employee->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            {{-- Form Actions --}}
            <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary">
                    Save Changes
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
