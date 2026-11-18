@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="container-fluid py-3 d-flex flex-column align-items-center">
    <div class="text-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Forgot Password</h1>
            <p class="text-secondary mb-0">
                Enter your registered email address to request a password reset link.
            </p>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 mb-4 w-100" style="max-width: 560px;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <i class="bi bi-shield-lock text-primary fs-5"></i>
                <h2 class="h5 fw-bold text-dark mb-0">Request a Password Reset</h2>
            </div>
            <p class="text-secondary text-center mb-4">We’ll send you a link to reset your password.</p>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="form-label text-secondary small mb-1 d-block mx-auto" style="max-width: 420px;">Email Address</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        maxlength="255"
                        autocomplete="email"
                        class="form-control mx-auto"
                        style="max-width: 420px;"
                        required
                        autofocus
                    >
                </div>

                <div class="border-top pt-4 d-flex flex-wrap align-items-center justify-content-center gap-3">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-envelope me-1"></i>
                        Send Password Reset Link
                    </button>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
