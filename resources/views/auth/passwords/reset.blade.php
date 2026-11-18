
@extends('layouts.app')

@section('title', 'Set Up Password')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-7 col-xl-6">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title">Set Up Password</h1>
            <p class="page-subtitle mb-0">
                Create a secure password to activate your account.
            </p>
        </div>
    </div>

    {{-- Password Setup Form --}}
    <div class="form-card">

        {{-- Form Heading --}}
        <div class="mb-4 pb-3 border-bottom">
            <h2 class="h5 fw-bold mb-1">
                <i class="bi bi-shield-lock me-2 text-primary"></i>
                Create Your Password
            </h2>

            <p class="text-secondary mb-0">
                Enter your new password and confirm it to continue.
            </p>
        </div>

        {{-- Success Message --}}
        @if (session('status'))
            <div class="alert alert-success" role="status">
                {{ session('status') }}
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please correct the following errors:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Password Setup Form --}}
        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label for="email" class="form-label">
                    Email Address <span class="text-danger">*</span>
                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $email) }}"
                    placeholder="Enter your registered email"
                    autocomplete="email"
                    required
                >

                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    New Password <span class="text-danger">*</span>
                </label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Enter your new password"
                    autocomplete="new-password"
                    minlength="12"
                    required
                >

                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="form-text">
                    Use at least 12 characters and follow any additional password requirements.
                </div>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">
                    Confirm Password <span class="text-danger">*</span>
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="form-control"
                    placeholder="Re-enter your new password"
                    autocomplete="new-password"
                    minlength="12"
                    required
                >
            </div>

            {{-- Form Actions --}}
            <div class="d-flex flex-wrap gap-2 mt-4 pt-4 border-top">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-shield-check me-1"></i>
                    Set Up My Password
                </button>

                <a href="{{ route('login') }}"
                   class="btn btn-light border">
                    Cancel
                </a>
            </div>
        </form>
    </div>
        </div>
    </div>
</div>
@endsection
