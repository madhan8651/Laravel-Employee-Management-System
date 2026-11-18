@extends('layouts.guest')

@section('title', 'Sign In | Employee Management System')

@push('styles')
<style>
    /* Login page only — does not affect authenticated pages */
    .auth-page {
        --auth-navy: #10213b;
        --auth-blue: #2563eb;
        --auth-border: #dbe3ef;

        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px 20px;
        background:
            radial-gradient(ellipse at top left, #dbeafe 0%, transparent 45%),
            #f1f5f9;
        font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
    }

    .auth-page *,
    .auth-page *::before,
    .auth-page *::after {
        box-sizing: border-box;
    }

    .auth-shell {
        width: 100%;
        max-width: 960px;
        min-height: 570px;
        display: grid;
        grid-template-columns: 0.95fr 1.05fr;
        overflow: hidden;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        box-shadow: 0 24px 70px rgb(15 23 42 / 12%);
    }

    /* Corporate branding panel */
    .auth-brand {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
        padding: 42px 36px;
        color: #fff;
        background:
            radial-gradient(circle at 90% 10%, rgb(59 130 246 / 30%), transparent 35%),
            linear-gradient(145deg, #10213b, #172f55 65%, #1e40af);
    }

    .auth-brand::after {
        position: absolute;
        right: -100px;
        bottom: 70px;
        width: 260px;
        height: 260px;
        border: 1px solid rgb(255 255 255 / 12%);
        border-radius: 50%;
        box-shadow:
            0 0 0 35px rgb(255 255 255 / 4%),
            0 0 0 70px rgb(255 255 255 / 3%);
        content: "";
        pointer-events: none;
    }

    .auth-brand-top,
    .auth-brand-copy,
    .auth-brand-footer {
        position: relative;
        z-index: 1;
    }

    .auth-brand-logo {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        margin-bottom: 24px;
        color: #fff;
        background: rgb(255 255 255 / 12%);
        border: 1px solid rgb(255 255 255 / 18%);
        border-radius: 14px;
        font-size: 21px;
        font-weight: 800;
        letter-spacing: -1px;
    }

    .auth-brand-eyebrow {
        margin-bottom: 12px;
        color: #bfdbfe;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .auth-brand h2 {
        margin: 0 0 18px;
        color: #fff;
        font-size: clamp(28px, 3vw, 38px);
        font-weight: 750;
        line-height: 1.2;
        letter-spacing: -1px;
    }

    .auth-brand-copy > p {
        max-width: 340px;
        margin: 0;
        color: #d5e2f5;
        font-size: 15px;
        line-height: 1.8;
    }

    .auth-brand-footer {
        padding-top: 28px;
        color: #c4d3e9;
        font-size: 12px;
    }

    /* Login form panel */
    .auth-form-panel {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 48px 46px;
        min-width: 0;
    }

    .auth-form-heading {
        margin-bottom: 30px;
    }

    .auth-form-heading .auth-kicker {
        margin: 0 0 10px;
        color: var(--auth-blue);
        font-size: 12px;
        font-weight: 750;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    .auth-form-heading h1 {
        margin: 0 0 10px;
        color: var(--auth-navy);
        font-size: 30px;
        font-weight: 750;
        line-height: 1.25;
        letter-spacing: -0.8px;
    }

    .auth-form-heading > p:last-child {
        margin: 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
    }

    .auth-field {
        margin-bottom: 21px;
    }

    .auth-page .auth-field label {
        display: block;
        margin: 0 0 8px;
        color: #26364d;
        font-size: 13px;
        font-weight: 700;
    }

    .auth-page .auth-field input {
        display: block;
        width: 100%;
        min-height: 49px;
        margin: 0;
        padding: 12px 14px;
        color: #17253b;
        background: #fff;
        border: 1px solid var(--auth-border);
        border-radius: 10px;
        outline: none;
        font: inherit;
        font-size: 14px;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .auth-page .auth-field input::placeholder {
        color: #94a3b8;
    }

    .auth-page .auth-field input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgb(37 99 235 / 12%);
    }

    .auth-submit {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 49px;
        margin-top: 8px;
        padding: 12px 18px;
        color: #fff;
        background: linear-gradient(100deg, #1d4ed8, #2563eb);
        border: 1px solid #2563eb;
        border-radius: 10px;
        box-shadow: 0 5px 12px rgb(37 99 235 / 20%);
        cursor: pointer;
        font: inherit;
        font-size: 14px;
        font-weight: 700;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .auth-submit:hover {
        background: linear-gradient(100deg, #1e40af, #1d4ed8);
        box-shadow: 0 8px 18px rgb(37 99 235 / 25%);
        transform: translateY(-1px);
    }

    .auth-submit:focus-visible,
    .auth-back-link:focus-visible {
        outline: 3px solid #93c5fd;
        outline-offset: 3px;
    }

    .auth-notice {
        margin: 0 0 20px;
        padding: 12px 14px;
        border: 1px solid transparent;
        border-radius: 9px;
        font-size: 13px;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }

    .auth-notice p {
        margin: 0;
    }

    .auth-notice p + p {
        margin-top: 5px;
    }

    .auth-notice--error {
        color: #991b1b;
        background: #fef2f2;
        border-color: #fecaca;
    }

    .auth-notice--success {
        color: #166534;
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .auth-back {
        margin: 24px 0 0;
        text-align: center;
        font-size: 13px;
    }

    .auth-back-link {
        color: #475569;
        text-decoration: none;
        font-weight: 600;
    }

    .auth-back-link:hover {
        color: var(--auth-blue);
        text-decoration: underline;
    }

    .auth-security-note {
        margin: 24px 0 0;
        color: #94a3b8;
        font-size: 11px;
        line-height: 1.6;
        text-align: center;
    }

    .auth-forgot-link {
        display: block;
        margin: -8px 0 18px;
        color: #2563eb;
        font-size: 13px;
        font-weight: 600;
        text-align: right;
        text-decoration: none;
    }

    .auth-forgot-link:hover {
        color: #1e40af;
        text-decoration: underline;
    }

    @media (max-width: 700px) {
        .auth-page {
            min-height: 100vh;
            padding: 18px 12px;
        }

        .auth-shell {
            max-width: 460px;
            min-height: auto;
            grid-template-columns: 1fr;
            border-radius: 18px;
        }

        .auth-brand {
            min-height: 190px;
            padding: 24px;
        }

        .auth-brand-logo {
            width: 40px;
            height: 40px;
            margin-bottom: 16px;
        }

        .auth-brand h2 {
            margin-bottom: 8px;
            font-size: 25px;
        }

        .auth-brand-copy > p {
            font-size: 13px;
        }

        .auth-brand-footer {
            padding-top: 22px;
        }

        .auth-form-panel {
            padding: 30px 24px;
        }

        .auth-form-heading {
            margin-bottom: 24px;
        }

        .auth-form-heading h1 {
            font-size: 27px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .auth-page *,
        .auth-page *::before,
        .auth-page *::after {
            transition: none !important;
            transform: none !important;
        }
    }
    
.alert {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 18px;
    font-size: 14px;
    line-height: 1.5;
}

.alert-success {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert-warning {
    background: #fffbeb;
    color: #92400e;
    border: 1px solid #fde68a;
}

</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-shell">
        <section class="auth-brand" aria-label="Employee Management System">
            <div class="auth-brand-top">
                <div class="auth-brand-logo" aria-hidden="true">EM</div>
                <div class="auth-brand-eyebrow">People · Operations · Growth</div>
            </div>

            <div class="auth-brand-copy">
                <h2>Better people management starts here.</h2>
                <p>
                    A central workspace for managing employees, departments,
                    and the work that keeps your organisation moving.
                </p>
            </div>

            <div class="auth-brand-footer">
                Employee Management System
            </div>
        </section>

        <section class="auth-form-panel">
            <div class="auth-form-heading">
                <p class="auth-kicker">Welcome back</p>
                <h1>Sign in to your account</h1>
                <p>Enter your credentials to access your workspace.</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning" role="alert">
                    {{ session('warning') }}
                </div>
            @endif

            @if (session('error'))
                <div class="auth-notice auth-notice--error" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="auth-notice auth-notice--error" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                
<div class="auth-field">
    <label for="login">Email address</label>
    <input
        id="login"
        type="text"
        name="login"
        value="{{ old('login') }}"
        placeholder="you@company.com"
        autocomplete="username"
        spellcheck="false"
        required
        autofocus
    >
</div>


                <div class="auth-field">
                    <label for="password">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <a class="auth-forgot-link" href="{{ route('password.request') }}">
                    Forgot Password?
                </a>
                
<div class="auth-field" style="margin-bottom: 12px;">
    <label for="remember" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
        <input
            id="remember"
            type="checkbox"
            name="remember"
            value="1"
            style="width: 16px; min-height: 16px; margin: 0;"
            {{ old('remember') ? 'checked' : '' }}
        >
        Remember me
    </label>
</div>


                <button class="auth-submit" type="submit">
                    Sign in securely
                </button>
            </form>

            <p class="auth-back">
                <a class="auth-back-link" href="{{ url('/') }}">
                    &larr; Back to Home
                </a>
            </p>

            <p class="auth-security-note">
                Secure access for authorised users only.
            </p>
        </section>
    </div>
</div>
@endsection
