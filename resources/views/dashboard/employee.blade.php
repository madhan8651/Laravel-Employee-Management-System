@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')

<div class="container-fluid px-3 px-lg-4 py-4 employee-dashboard-page">

    {{-- Employee Dashboard Header --}}
    <div class="employee-dashboard-hero mb-4">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-8">
                <span class="employee-hero-label">EMPLOYEE WORKSPACE</span>

                <h1 class="employee-hero-title mt-3 mb-2">
                    Welcome back, {{ $employee?->name ?? auth()->user()->name }}!
                </h1>

                <p class="employee-hero-description mb-0">
                    Your personal workspace to view employment details and stay up to date.
                </p>
            </div>

            <div class="col-12 col-lg-4 text-lg-end">
                <div class="employee-hero-status">
                    <span class="employee-status-dot"></span>
                    Employee Dashboard
                </div>

                <p class="employee-hero-date mb-0 mt-3">
                    <i class="bi bi-calendar3 me-2"></i>
                    {{ now()->format('l, d M Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Employee Information --}}
    <div class="employee-section-header mb-3">
        <div>
            <h2 class="employee-section-heading">My Work Information</h2>
            <p class="employee-section-description">
                An overview of your employment details.
            </p>
        </div>
    </div>

    <div class="row g-3 g-xl-4 mb-4">

        {{-- Employee Code --}}
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="employee-info-card h-100">
                <div class="employee-card-top">
                    <div>
                        <p class="employee-card-label">Employee Code</p>
                        <h3 class="employee-card-value">
                            {{ $employee?->employee_code ?? 'Not assigned' }}
                        </h3>
                    </div>

                    <div class="employee-card-icon employee-icon-code">
                        <i class="bi bi-person-badge"></i>
                    </div>
                </div>
                <p class="employee-card-caption">Your unique employee identification</p>
            </div>
        </div>

        {{-- Designation --}}
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="employee-info-card h-100">
                <div class="employee-card-top">
                    <div>
                        <p class="employee-card-label">Designation</p>
                        <h3 class="employee-card-value">
                            {{ $employee?->designation ?? 'Not assigned' }}
                        </h3>
                    </div>

                    <div class="employee-card-icon employee-icon-designation">
                        <i class="bi bi-briefcase"></i>
                    </div>
                </div>
                <p class="employee-card-caption">Your current job position</p>
            </div>
        </div>

        {{-- Department --}}
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="employee-info-card h-100">
                <div class="employee-card-top">
                    <div>
                        <p class="employee-card-label">Department</p>
                        <h3 class="employee-card-value">
                            {{ $employee?->department?->name ?? 'Not assigned' }}
                        </h3>
                    </div>

                    <div class="employee-card-icon employee-icon-department">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                </div>
                <p class="employee-card-caption">Your assigned department</p>
            </div>
        </div>

        {{-- Joining Date --}}
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="employee-info-card h-100">
                <div class="employee-card-top">
                    <div>
                        <p class="employee-card-label">Joining Date</p>
                        <h3 class="employee-card-value">
                            {{ $employee?->date_of_joining?->format('d M Y') ?? 'Not available' }}
                        </h3>
                    </div>

                    <div class="employee-card-icon employee-icon-date">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
                <p class="employee-card-caption">Your date of joining</p>
            </div>
        </div>

        {{-- Employment Status --}}
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="employee-info-card h-100">
                <div class="employee-card-top">
                    <div>
                        <p class="employee-card-label">Employment Status</p>
                        <h3 class="employee-card-value">
                            {{ ucfirst($employee?->status?->value ?? $employee?->status ?? 'Not available') }}
                        </h3>
                    </div>

                    <div class="employee-card-icon employee-icon-status">
                        <i class="bi bi-person-check"></i>
                    </div>
                </div>
                <p class="employee-card-caption">Your current employment status</p>
            </div>
        </div>

    </div>
</div>

<style>
/* ==========================================
   EMPLOYEE DASHBOARD - ENTERPRISE THEME
========================================== */

.employee-dashboard-page {
    color: #263447;
}

/* Dashboard hero */
.employee-dashboard-hero {
    position: relative;
    overflow: hidden;
    padding: 34px 36px;
    background: linear-gradient(
        115deg,
        #1C2B3F 0%,
        #293D55 58%,
        #354B64 100%
    );
    border: 1px solid #40546C;
    border-radius: 18px;
    box-shadow: 0 10px 28px rgba(25, 39, 57, 0.12);
}

.employee-dashboard-hero::after {
    content: "";
    position: absolute;
    top: -85px;
    right: 150px;
    width: 240px;
    height: 240px;
    border: 1px solid rgba(218, 228, 240, 0.09);
    border-radius: 50%;
    box-shadow:
        0 0 0 28px rgba(218, 228, 240, 0.025),
        0 0 0 58px rgba(218, 228, 240, 0.02);
    pointer-events: none;
}

.employee-dashboard-hero .row {
    position: relative;
    z-index: 1;
}

.employee-hero-label {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    color: #B8C9DC;
    font-size: 0.7rem;
    font-weight: 750;
    letter-spacing: 2px;
}

.employee-hero-label::before {
    content: "";
    width: 22px;
    height: 2px;
    background: #8FA9C3;
    border-radius: 2px;
}

.employee-hero-title {
    color: #FFFFFF !important;
    font-size: clamp(1.65rem, 2.7vw, 2.15rem);
    font-weight: 750;
    line-height: 1.25;
    letter-spacing: -0.8px;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
}

.employee-hero-description {
    max-width: 560px;
    color: #D0DAE6;
    font-size: 0.92rem;
    line-height: 1.75;
}

.employee-hero-status {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 11px 15px;
    color: #E8EEF5;
    background: rgba(12, 24, 39, 0.25);
    border: 1px solid rgba(202, 218, 235, 0.2);
    border-radius: 11px;
    font-size: 0.82rem;
    font-weight: 600;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
}

.employee-status-dot {
    width: 8px;
    height: 8px;
    flex-shrink: 0;
    background: #9BB9A6;
    border-radius: 50%;
    box-shadow: 0 0 0 4px rgba(155, 185, 166, 0.13);
}

.employee-hero-date {
    color: #CAD6E4;
    font-size: 0.82rem;
}

.employee-hero-date i {
    color: #9FB4CA;
}

/* Section headings */
.employee-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.employee-section-heading {
    margin-bottom: 5px;
    color: #263447;
    font-size: 1.12rem;
    font-weight: 750;
    letter-spacing: -0.25px;
}

.employee-section-description {
    margin-bottom: 0;
    color: #687588;
    font-size: 0.86rem;
    line-height: 1.6;
}

/* Information cards */
.employee-info-card {
    height: 100%;
    padding: 23px;
    background: #DDE4EC;
    border: 1px solid #CDD6E0;
    border-radius: 14px;
    box-shadow: 0 2px 7px rgba(28, 39, 55, 0.025);
    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease,
        border-color 0.2s ease;
}

.employee-info-card:hover {
    transform: translateY(-3px);
    border-color: #B9C6D5;
    box-shadow: 0 8px 20px rgba(28, 39, 55, 0.055);
}

.employee-card-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}

.employee-card-label {
    margin-bottom: 13px;
    color: #58677B;
    font-size: 0.83rem;
    font-weight: 650;
}

.employee-card-value {
    margin: 0;
    color: #263447;
    font-size: clamp(1.15rem, 2vw, 1.4rem);
    font-weight: 800;
    letter-spacing: -0.5px;
    line-height: 1.35;
    overflow-wrap: anywhere;
}

.employee-card-caption {
    margin: 13px 0 0;
    color: #687588;
    font-size: 0.79rem;
    line-height: 1.55;
}

/* Card icons */
.employee-card-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 44px;
    width: 44px;
    height: 44px;
    border: 1px solid #C7D1DE;
    border-radius: 11px;
    font-size: 1.05rem;
}

.employee-icon-code {
    color: #465F7B;
    background: #D0DAE5;
}

.employee-icon-designation {
    color: #507365;
    background: #D1DFD8;
    border-color: #C0D2C9;
}

.employee-icon-department {
    color: #696387;
    background: #DAD8E6;
    border-color: #CCC9DE;
}

.employee-icon-date {
    color: #916C5F;
    background: #E4D9D3;
    border-color: #D8C9C1;
}

.employee-icon-status {
    color: #526B87;
    background: #D0DAE5;
}

/* Responsive adjustments */
@media (max-width: 575.98px) {
    .employee-dashboard-page {
        padding-top: 18px !important;
    }

    .employee-dashboard-hero {
        padding: 25px 21px;
        border-radius: 14px;
    }

    .employee-dashboard-hero::after {
        right: -100px;
    }

    .employee-hero-title {
        font-size: 1.55rem;
    }

    .employee-hero-status {
        margin-top: 5px;
    }

    .employee-info-card {
        padding: 20px;
        border-radius: 12px;
    }

    .employee-card-value {
        font-size: 1.2rem;
    }
}

@media (prefers-reduced-motion: reduce) {
    .employee-info-card {
        transition: none;
    }
}
</style>

@endsection