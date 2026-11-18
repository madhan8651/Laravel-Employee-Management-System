@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid px-0">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title">Edit User</h1>
            <p class="page-subtitle mb-0">
                Update the user's account information.
            </p>
        </div>

        <a href="{{ route('admin.users.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Users
        </a>
    </div>

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

    {{-- Edit User Form --}}
    <div class="form-card">

        {{-- Account Information Heading Card --}}
        <div class="card shadow-sm rounded-3 mb-4"
             style="display: inline-flex; border: 1px solid #dee2e6;">
            <div class="card-body py-2 px-3">
                <h2 class="h6 fw-bold mb-0">
                    Account Information
                </h2>
            </div>
        </div>

        <form method="POST"
              action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="row g-4">

                {{-- Full Name --}}
                <div class="col-12 col-md-6">
                    <label for="name" class="form-label">
                        Full Name <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        class="form-control @error('name') is-invalid @enderror"
                        maxlength="255"
                        autocomplete="name"
                        placeholder="Enter full name"
                        required
                        autofocus
                    >

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email Address --}}
                <div class="col-12 col-md-6">
                    <label for="email" class="form-label">
                        Email Address <span class="text-danger">*</span>
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        class="form-control @error('email') is-invalid @enderror"
                        maxlength="255"
                        autocomplete="email"
                        placeholder="example@company.com"
                        required
                    >

                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Optional Password --}}
                <div class="col-12 col-md-6">
                    <label for="password" class="form-label">
                        New Password
                        <span class="text-secondary fw-normal">(Optional)</span>
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        minlength="12"
                        autocomplete="new-password"
                        placeholder="Enter a new password if needed"
                    >

                    <div class="form-text">
                        Leave blank to keep the current password.
                        If changing it, use at least 12 characters.
                    </div>

                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            {{-- Form Actions --}}
            <div class="d-flex flex-wrap gap-2 mt-4 pt-4 border-top">

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle me-1"></i>
                    Save Changes
                </button>

                <a href="{{ route('admin.users.show', $user) }}"
                   class="btn btn-light border">
                    Cancel
                </a>

            </div>

        </form>
    </div>

</div>
@endsection
