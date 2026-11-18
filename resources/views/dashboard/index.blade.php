@extends('layouts.app')

@section('title', __('dashboard.page_title'))

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4 dashboard-page">

    {{-- Dashboard Header --}}
    <div class="dashboard-hero mb-4">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-8">
                <span class="hero-label">{{ __('dashboard.administration') }}</span>

                <h1 class="hero-title mt-3 mb-2">
                    {{ __('dashboard.welcome_back', ['name' => $user->name]) }}
                </h1>

                <p class="hero-description mb-0">
                    {{ __('dashboard.hero_description') }}
                </p>
            </div>

            <div class="col-12 col-lg-4 text-lg-end">
                <div class="hero-status">
                    <span class="status-dot"></span>
                    {{ __('dashboard.dashboard_overview') }}
                </div>

                <p class="hero-date mb-0 mt-3">
                    <i class="bi bi-calendar3 me-2"></i>
                    {{ now()->format('l, d M Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- System Overview --}}
    <div class="section-header mb-3">
        <div>
            <h2 class="section-heading">{{ __('dashboard.system_overview') }}</h2>
            <p class="section-description">
                {{ __('dashboard.system_description') }}
            </p>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3 g-xl-4 mb-5">

        {{-- Total Employees --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="stat-top">
                    <div>
                        <p class="stat-label">{{ __('dashboard.total_employees') }}</p>
                        <h2 class="stat-number">
                            {{ number_format($totalEmployees) }}
                        </h2>
                    </div>

                    <div class="stat-icon icon-employees">
                        <i class="bi bi-people"></i>
                    </div>
                </div>

                <p class="stat-caption">
                    {{ __('dashboard.registered_employee_records') }}
                </p>

                <div class="stat-footer">
                    <span class="stat-footer-text">{{ __('dashboard.employee_directory') }}</span>
                    <a href="{{ route('admin.employees.index') }}"
                       class="stat-link">
                        {{ __('dashboard.view') }} <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Active Employees --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="stat-top">
                    <div>
                        <p class="stat-label">{{ __('dashboard.active_employees') }}</p>
                        <h2 class="stat-number">
                            {{ number_format($activeEmployees) }}
                        </h2>
                    </div>

                    <div class="stat-icon icon-active">
                        <i class="bi bi-person-check"></i>
                    </div>
                </div>

                <p class="stat-caption">
                    {{ __('dashboard.currently_active_employees') }}
                </p>

                <div class="stat-footer">
                    <span class="stat-footer-text">{{ __('dashboard.workforce_status') }}</span>
                    <span class="stat-indicator indicator-active">
                        <span></span> {{ __('dashboard.active') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Inactive Employees --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="stat-top">
                    <div>
                        <p class="stat-label">{{ __('dashboard.inactive_employees') }}</p>
                        <h2 class="stat-number">
                            {{ number_format($inactiveEmployees) }}
                        </h2>
                    </div>

                    <div class="stat-icon icon-inactive">
                        <i class="bi bi-person-dash"></i>
                    </div>
                </div>

                <p class="stat-caption">
                    {{ __('dashboard.currently_inactive_employees') }}
                </p>

                <div class="stat-footer">
                    <span class="stat-footer-text">{{ __('dashboard.workforce_status') }}</span>
                    <span class="stat-indicator indicator-inactive">
                        <span></span> {{ __('dashboard.inactive') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Total Departments --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card h-100">
                <div class="stat-top">
                    <div>
                        <p class="stat-label">{{ __('dashboard.total_departments') }}</p>
                        <h2 class="stat-number">
                            {{ number_format($totalDepartments) }}
                        </h2>
                    </div>

                    <div class="stat-icon icon-departments">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                </div>

                <p class="stat-caption">
                    {{ __('dashboard.departments_in_organization') }}
                </p>

                <div class="stat-footer">
                    <span class="stat-footer-text">{{ __('dashboard.organization_structure') }}</span>
                    <a href="{{ route('admin.departments.index') }}"
                       class="stat-link">
                        {{ __('dashboard.view') }} <i class="bi bi-arrow-up-right"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- Employees by Department --}}
    <div class="section-header mb-3">
        <div>
            <h2 class="section-heading">{{ __('dashboard.employees_by_department') }}</h2>
            <p class="section-description">
                {{ __('dashboard.employee_distribution') }}
            </p>
        </div>
    </div>

    <div class="department-panel mb-4">
        @if (count($departmentStats) > 0)
            @php
                $maxDepartmentEmployees = max(
                    1,
                    collect($departmentStats)->max('employees_count')
                );
            @endphp

            <div class="row g-4">
                @foreach ($departmentStats as $department)
                    @php
                        $employeeCount = (int) $department['employees_count'];

                        $percentage = ($employeeCount / $maxDepartmentEmployees) * 100;
                    @endphp

                    <div class="col-12 col-md-6">
                        <div class="department-item">
                            <div class="department-info">
                                <span class="department-name">
                                    {{ $department['name'] }}
                                </span>

                                <span class="department-count">
                                    {{ number_format($employeeCount) }}
                                    {{ $employeeCount === 1
                                        ? __('dashboard.employee')
                                        : __('dashboard.employees') }}
                                </span>
                            </div>

                            <div class="department-progress">
                                <div
                                    class="department-progress-bar"
                                    role="progressbar"
                                    style="width: {{ $percentage }}%"
                                    aria-valuenow="{{ $employeeCount }}"
                                    aria-valuemin="0"
                                    aria-valuemax="{{ $maxDepartmentEmployees }}"
                                    aria-label="{{ __('dashboard.employee_count', ['name' => $department['name']]) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-departments">
                <div class="empty-icon">
                    <i class="bi bi-diagram-3"></i>
                </div>

                <h3>{{ __('dashboard.no_department_data') }}</h3>

                <p>
                    {{ __('dashboard.department_statistics_empty') }}
                </p>
            </div>
        @endif
    </div>

</div>

<style>
/* ==========================================
   DASHBOARD - MUTED ENTERPRISE THEME
========================================== */

.dashboard-page {
    color: #263447;
}

/* Premium welcome banner */
.dashboard-hero {
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

/* Soft decorative accent */
.dashboard-hero::after {
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

.dashboard-hero .row {
    position: relative;
    z-index: 1;
}

.hero-label {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    color: #B8C9DC;
    font-size: 0.7rem;
    font-weight: 750;
    letter-spacing: 2px;
}

.hero-label::before {
    content: "";
    width: 22px;
    height: 2px;
    background: #8FA9C3;
    border-radius: 2px;
}

.hero-title {
    position: relative;
    z-index: 2;
    color: #FFFFFF !important;
    font-size: clamp(1.65rem, 2.7vw, 2.15rem);
    font-weight: 750;
    line-height: 1.25;
    letter-spacing: -0.8px;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
}

.hero-description {
    max-width: 560px;
    color: #D0DAE6;
    font-size: 0.92rem;
    line-height: 1.75;
}

.hero-status {
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

.status-dot {
    width: 8px;
    height: 8px;
    flex-shrink: 0;
    background: #9BB9A6;
    border-radius: 50%;
    box-shadow: 0 0 0 4px rgba(155, 185, 166, 0.13);
}

.hero-date {
    color: #CAD6E4;
    font-size: 0.82rem;
}

.hero-date i {
    color: #9FB4CA;
}

/* Section headings */
.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.section-heading {
    margin-bottom: 5px;
    color: #263447;
    font-size: 1.12rem;
    font-weight: 750;
    letter-spacing: -0.25px;
}

.section-description {
    margin-bottom: 0;
    color: #687588;
    font-size: 0.86rem;
    line-height: 1.6;
}

/* Statistics cards */
.stat-card {
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

.stat-card:hover {
    transform: translateY(-3px);
    border-color: #B9C6D5;
    box-shadow: 0 8px 20px rgba(28, 39, 55, 0.055);
}

.stat-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}

.stat-label {
    margin-bottom: 13px;
    color: #58677B;
    font-size: 0.83rem;
    font-weight: 650;
}

.stat-number {
    margin: 0;
    color: #263447;
    font-size: clamp(2rem, 3vw, 2.5rem);
    font-weight: 800;
    letter-spacing: -1.2px;
    line-height: 1.1;
    overflow-wrap: anywhere;
}

.stat-caption {
    margin: 13px 0 0;
    color: #687588;
    font-size: 0.79rem;
    line-height: 1.55;
}

/* Muted icon containers */
.stat-icon {
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

.icon-employees {
    color: #465F7B;
    background: #D0DAE5;
}

.icon-active {
    color: #507365;
    background: #D1DFD8;
    border-color: #C0D2C9;
}

.icon-inactive {
    color: #916C5F;
    background: #E4D9D3;
    border-color: #D8C9C1;
}

.icon-departments {
    color: #696387;
    background: #DAD8E6;
    border-color: #CCC9DE;
}

/* Card footer */
.stat-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 21px;
    padding-top: 15px;
    border-top: 1px solid #CAD4DF;
}

.stat-footer-text {
    color: #748196;
    font-size: 0.73rem;
}

.stat-link {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #465F7B;
    font-size: 0.78rem;
    font-weight: 700;
    text-decoration: none;
    transition: color 0.2s ease;
}

.stat-link:hover {
    color: #263D57;
}

.stat-link i {
    transition: transform 0.2s ease;
}

.stat-link:hover i {
    transform: translate(2px, -2px);
}

/* Muted employee status labels */
.stat-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.74rem;
    font-weight: 650;
}

.stat-indicator span {
    width: 7px;
    height: 7px;
    border-radius: 50%;
}

.indicator-active {
    color: #507365;
}

.indicator-active span {
    background: #779C8A;
}

.indicator-inactive {
    color: #916C5F;
}

.indicator-inactive span {
    background: #B58A79;
}

/* Department distribution panel */
.department-panel {
    padding: 24px;
    background: #DDE4EC;
    border: 1px solid #CDD6E0;
    border-radius: 14px;
    box-shadow: 0 2px 7px rgba(28, 39, 55, 0.025);
}

.department-item {
    padding: 2px 0;
}

.department-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 10px;
}

.department-name {
    color: #344256;
    font-size: 0.86rem;
    font-weight: 650;
    overflow-wrap: anywhere;
}

.department-count {
    flex-shrink: 0;
    color: #687588;
    font-size: 0.78rem;
}

.department-progress {
    width: 100%;
    height: 8px;
    overflow: hidden;
    background: #C6D0DC;
    border-radius: 999px;
}

.department-progress-bar {
    height: 100%;
    min-width: 0;
    background: #526B87;
    border-radius: 999px;
    transition: width 0.35s ease;
}

/* Empty state */
.empty-departments {
    padding: 24px 12px;
    text-align: center;
}

.empty-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    margin-bottom: 12px;
    color: #526B87;
    background: #D0DAE5;
    border: 1px solid #C3CEDA;
    border-radius: 12px;
    font-size: 1.2rem;
}

.empty-departments h3 {
    color: #344256;
    font-size: 0.95rem;
    font-weight: 700;
}

.empty-departments p {
    margin: 0;
    color: #687588;
    font-size: 0.82rem;
}

/* Responsive adjustments */
@media (max-width: 575.98px) {
    .dashboard-page {
        padding-top: 18px !important;
    }

    .stat-card {
        padding: 20px;
        border-radius: 12px;
    }

    .stat-number {
        font-size: 2rem;
    }

    .department-panel {
        padding: 19px;
        border-radius: 12px;
    }

    .department-info {
        align-items: flex-start;
    }

    .dashboard-hero {
        padding: 25px 21px;
        border-radius: 14px;
    }

    .dashboard-hero::after {
        right: -100px;
    }

    .hero-title {
        font-size: 1.55rem;
    }

    .hero-status {
        margin-top: 5px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .stat-card,
    .stat-link i,
    .department-progress-bar {
        transition: none;
    }
}
</style>
@endsection