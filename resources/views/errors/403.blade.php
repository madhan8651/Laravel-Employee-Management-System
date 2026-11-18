
@extends('layouts.app')

@section('title', '403 - Access Forbidden')

@section('content')
<style>
    .forbidden-page {
        min-height: 75vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 16px;
        background: linear-gradient(135deg, #f1f5ff, #f8fafc);
        border-radius: 20px;
    }

    .forbidden-card {
        width: 100%;
        max-width: 560px;
        padding: 48px 35px;
        text-align: center;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
    }

    .forbidden-icon {
        width: 84px;
        height: 84px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        border-radius: 50%;
        background: #fee2e2;
        color: #dc2626;
        font-size: 38px;
        font-weight: 800;
    }

    .forbidden-code {
        margin: 0;
        color: #dc2626;
        font-size: clamp(48px, 8vw, 68px);
        font-weight: 800;
        line-height: 1.1;
    }

    .forbidden-title {
        margin: 14px 0;
        color: #0f172a;
        font-size: 27px;
        font-weight: 700;
    }

    .forbidden-description {
        max-width: 410px;
        margin: 0 auto 28px;
        color: #64748b;
        font-size: 16px;
        line-height: 1.7;
    }

    .forbidden-actions {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .forbidden-btn {
        display: inline-block;
        padding: 13px 22px;
        border-radius: 10px;
        text-decoration: none !important;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .forbidden-btn-primary {
        background: #2563eb;
        color: #ffffff !important;
    }

    .forbidden-btn-primary:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
    }

    .forbidden-btn-secondary {
        background: #f1f5f9;
        color: #334155 !important;
    }

    .forbidden-btn-secondary:hover {
        background: #e2e8f0;
    }

    .forbidden-footer {
        margin: 26px 0 0;
        color: #94a3b8;
        font-size: 12px;
    }

    @media (max-width: 480px) {
        .forbidden-card {
            padding: 35px 20px;
        }

        .forbidden-title {
            font-size: 23px;
        }

        .forbidden-actions {
            flex-direction: column;
        }

        .forbidden-btn {
            width: 100%;
        }
    }
</style>

<div class="forbidden-page">
    <div class="forbidden-card">

        <div class="forbidden-icon" aria-hidden="true">
            !
        </div>

        <p class="forbidden-code">403</p>

        <h1 class="forbidden-title">
            Access Forbidden
        </h1>

        <p class="forbidden-description">
            You are signed in, but your account does not have
            permission to access this page. Please return to
            your dashboard.
        </p>

        <div class="forbidden-actions">
            <a href="{{ route('dashboard') }}"
               class="forbidden-btn forbidden-btn-primary">
                Return to Dashboard
            </a>

            <a href="{{ route('employee.profile.show') }}"
               class="forbidden-btn forbidden-btn-secondary">
                My Profile
            </a>
        </div>

        <p class="forbidden-footer">
            Error 403 · Access is restricted by your account permissions.
        </p>

    </div>
</div>
@endsection
