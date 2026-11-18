@extends('layouts.app')

@section('title', 'Employee Reports')

@section('content')
<div class="employee-reports-page">

    {{-- Page Header --}}
    <div class="reports-header">
        <div>
            <span class="reports-eyebrow">EMPLOYEE MANAGEMENT</span>
            <h1>Employee Reports</h1>
            <p>Search, filter, and review employee records in one place.</p>
        </div>

        <a href="{{ route('admin.reports.export', request()->only(['search', 'department_id'])) }}"
           class="report-export-btn">
            <span aria-hidden="true">↓</span>
            Export CSV
        </a>
    </div>

    @include('partials.alerts')

    {{-- Filters --}}
    <section class="report-panel filter-panel">
        <div class="report-panel-body">
            <form method="GET" action="{{ route('admin.reports.index') }}">
                <div class="report-filter-grid">

                    <div class="report-field search-field">
                        <label for="search">Search Employee</label>
                        <input
                            id="search"
                            type="search"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Name, employee code, or email"
                        >
                    </div>

                    <div class="report-field">
                        <label for="department_id">Department</label>

                        <select id="department_id" name="department_id">
                            <option value="">All Departments</option>

                            @foreach ($departments as $department)
                                <option
                                    value="{{ $department->id }}"
                                    @selected((string) ($filters['department_id'] ?? '') === (string) $department->id)
                                >
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="report-filter-actions">
    <button type="submit" class="report-btn report-btn-primary">
        Apply Filters
    </button>

    <a href="{{ route('admin.reports.index') }}"
       class="report-btn report-btn-secondary">
        Clear
    </a>
</div>

                </div>
            </form>
        </div>
    </section>

    {{-- Report Summary --}}
    <div class="report-summary-grid">

        <article class="report-summary-card">
            <div class="summary-top">
                <div>
                    <p class="summary-label">Matching Employees</p>
                    <h2>{{ $employees->count() }}</h2>
                </div>
                <div class="summary-icon employees-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM20 8v6M23 11h-6"
                              stroke="currentColor" stroke-width="1.7"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <div class="summary-footer">
                <span class="summary-dot"></span>
                Employees matching selected filters
            </div>
        </article>

        <article class="report-summary-card">
            <div class="summary-top">
                <div>
                    <p class="summary-label">Total Salary</p>
                    <h2 class="salary-total">
                        ₹{{ number_format((float) $totalSalary, 2) }}
                    </h2>
                </div>
                <div class="summary-icon salary-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="5" width="18" height="14" rx="2"
                              stroke="currentColor" stroke-width="1.7"/>
                        <circle cx="12" cy="12" r="3"
                                stroke="currentColor" stroke-width="1.7"/>
                        <path d="M7 9h.01M17 15h.01"
                              stroke="currentColor" stroke-width="2"
                              stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
            <div class="summary-footer">
                <span class="summary-dot salary-dot"></span>
                Combined salary of matching employees
            </div>
        </article>

    </div>

    {{-- Employee Report Table --}}
    <section class="report-panel employee-report-panel">
        <div class="report-panel-heading table-panel-heading">
            <div class="panel-heading-icon table-heading-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="4" width="18" height="16" rx="2"
                          stroke="currentColor" stroke-width="1.7"/>
                    <path d="M3 10h18M9 4v16M15 10v10"
                          stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </div>
            <div>
                <h2>Employee Report</h2>
                <p>Employee details based on your selected filters.</p>
            </div>
            <span class="report-count">{{ $employees->count() }} records</span>
        </div>

        @if ($employees->isEmpty())
            <div class="report-empty-state">
                <div class="empty-state-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="11" cy="11" r="7"
                                stroke="currentColor" stroke-width="1.7"/>
                        <path d="m16 16 4 4M8 11h6"
                              stroke="currentColor" stroke-width="1.7"
                              stroke-linecap="round"/>
                    </svg>
                </div>
                <h3>No employees found</h3>
                <p>No employees match your current filters. Try changing your search criteria.</p>
                <a href="{{ route('admin.reports.index') }}" class="report-btn report-btn-secondary">
                    Clear Filters
                </a>
            </div>
        @else
            <div class="report-table-wrap">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th scope="col" class="number-column">#</th>
                            <th scope="col">Employee Code</th>
                            <th scope="col">Employee</th>
                            <th scope="col">Email Address</th>
                            <th scope="col">Designation</th>
                            <th scope="col">Department</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td class="row-number">
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    <span class="employee-code-badge">
                                        {{ $employee->employee_code }}
                                    </span>
                                </td>
                                <td>
                                    <div class="employee-name-cell">
                                        <span class="employee-avatar" aria-hidden="true">
                                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                                        </span>
                                        <span class="employee-name">
                                            {{ $employee->name }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $employee->email }}" class="employee-email">
                                        {{ $employee->email }}
                                    </a>
                                </td>
                                <td>
                                    <span class="designation-text">
                                        {{ $employee->designation ?: '—' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($employee->department)
                                        <span class="department-badge">
                                            <span class="department-dot"></span>
                                            {{ $employee->department->name }}
                                        </span>
                                    @else
                                        <span class="unassigned-label">Unassigned</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <p class="report-page-note">
        <span aria-hidden="true">ⓘ</span>
        Report values reflect the employee records returned by the current filters.
    </p>

</div>

@push('styles')
/* =========================================
   EMPLOYEE REPORTS — GRAPHITE + STEEL
   All styles are scoped to this page.
========================================= */

.employee-reports-page {
    --report-navy: #202F43;
    --report-navy-deep: #172437;
    --report-steel: #53677F;
    --report-muted: #78879A;
    --report-border: #E0E6ED;
    --report-surface: #FFFFFF;
    --report-background: #F4F6F9;
    color: #263548;
    padding: 24px;
    min-width: 0;
    font-size: 13px;
}

.employee-reports-page * {
    box-sizing: border-box;
}

.reports-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 25px;
}

.reports-eyebrow {
    display: inline-block;
    margin-bottom: 8px;
    color: #71839A;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.6px;
}

.reports-header h1 {
    margin: 0 0 7px;
    color: var(--report-navy-deep);
    font-size: clamp(23px, 2.3vw, 29px);
    font-weight: 750;
    letter-spacing: -0.7px;
}

.reports-header p {
    margin: 0;
    color: var(--report-muted);
    font-size: 13px;
    line-height: 1.6;
}

.report-export-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    flex-shrink: 0;
    min-height: 42px;
    padding: 0 17px;
    border: 1px solid var(--report-navy);
    border-radius: 7px;
    background: var(--report-navy);
    color: #FFFFFF;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    transition: background .2s ease, transform .2s ease, box-shadow .2s ease;
}

.report-export-btn span {
    font-size: 18px;
    line-height: 1;
}

.report-export-btn:hover {
    background: var(--report-navy-deep);
    color: #FFFFFF;
    box-shadow: 0 5px 13px rgba(23, 36, 55, .15);
    transform: translateY(-1px);
}

.report-panel {
    overflow: hidden;
    margin-bottom: 22px;
    border: 1px solid var(--report-border);
    border-radius: 10px;
    background: var(--report-surface);
    box-shadow: 0 3px 12px rgba(28, 43, 62, .035);
}

.filter-panel {
    background: #FFFFFF;
}

.report-panel-heading {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 17px 21px;
    border-bottom: 1px solid var(--report-border);
    background: #FFFFFF;
}

.panel-heading-icon,
.summary-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.panel-heading-icon {
    width: 37px;
    height: 37px;
    border: 1px solid #DFE6EE;
    border-radius: 8px;
    background: #F1F4F8;
    color: var(--report-navy);
    font-size: 25px;
}

.report-panel-heading h2 {
    margin: 0 0 4px;
    color: var(--report-navy-deep);
    font-size: 14px;
    font-weight: 750;
}

.report-panel-heading p {
    margin: 0;
    color: var(--report-muted);
    font-size: 11px;
    line-height: 1.5;
}

.report-panel-body {
    padding: 22px;
}

.filter-panel .report-panel-body {
    padding-top: 0;
}

.report-filter-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr) max-content;
    align-items: end;
    gap: 17px;
}

.report-field {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 0;
}

.report-field label {
    color: #394B60;
    font-size: 12px;
    font-weight: 700;
}

.report-field input,
.report-field select {
    display: block;
    width: 100%;
    min-width: 0;
    height: 42px;
    padding: 0 12px;
    border: 1px solid #D8E0E9;
    border-radius: 6px;
    outline: none;
    background-color: #F1F3F5;
    color: #263548;
    font: inherit;
    font-size: 12px;
    transition: border-color .2s ease, box-shadow .2s ease;
}

.report-field input::placeholder {
    color: #A0ABBA;
}

.report-field input:focus,
.report-field select:focus {
    border-color: #637D9C;
    box-shadow: 0 0 0 3px rgba(80, 107, 140, .11);
}

.report-filter-actions {
    display: flex;
    gap: 8px;
}

.report-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 41px;
    padding: 0 15px;
    border: 1px solid transparent;
    border-radius: 6px;
    font-family: inherit;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.3;
    text-align: center;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
    transition: background .2s ease, border-color .2s ease, color .2s ease;
}

.report-btn-primary {
    border-color: var(--report-navy);
    background: var(--report-navy);
    color: #FFFFFF;
}

.report-btn-primary:hover {
    border-color: var(--report-navy-deep);
    background: var(--report-navy-deep);
    color: #FFFFFF;
}

.report-btn-secondary {
    border-color: #D8E0E9;
    background: #FFFFFF;
    color: #536477;
}

.report-btn-secondary:hover {
    border-color: #AAB7C7;
    background: #F2F5F8;
    color: #263548;
}

.report-summary-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 22px;
}

.report-summary-card {
    position: relative;
    min-width: 0;
    padding: 22px;
    overflow: hidden;
    border: 1px solid var(--report-border);
    border-radius: 10px;
    background: #FFFFFF;
    box-shadow: 0 3px 12px rgba(28, 43, 62, .035);
    transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
}

.report-summary-card::before {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    height: 3px;
    background: #526983;
    content: "";
}

.report-summary-card:nth-child(2)::before {
    background: #738B83;
}

.report-summary-card:hover {
    transform: translateY(-2px);
    border-color: #C7D2DE;
    box-shadow: 0 7px 20px rgba(28, 43, 62, .07);
}

.summary-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 15px;
}

.summary-label {
    margin: 0 0 11px;
    color: #718096;
    font-size: 12px;
    font-weight: 650;
}

.summary-top h2 {
    margin: 0;
    color: var(--report-navy-deep);
    font-size: 34px;
    font-weight: 800;
    line-height: 1.2;
    letter-spacing: -1px;
    overflow-wrap: anywhere;
}

.summary-top h2.salary-total {
    color: #466B5D;
    font-size: clamp(23px, 2.2vw, 31px);
    letter-spacing: -.5px;
}

.summary-icon {
    width: 46px;
    height: 46px;
    border: 1px solid #DFE6EE;
    border-radius: 9px;
    background: #F0F3F7;
    color: #3F5875;
}

.summary-icon svg {
    width: 23px;
    height: 23px;
}

.salary-icon {
    border-color: #DCE9E2;
    background: #EFF5F1;
    color: #527764;
}

.summary-footer {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 21px;
    padding-top: 14px;
    border-top: 1px solid #EDF0F4;
    color: #8491A1;
    font-size: 11px;
    line-height: 1.5;
}

.summary-dot,
.department-dot {
    width: 7px;
    height: 7px;
    flex-shrink: 0;
    border-radius: 50%;
    background: #647D99;
}

.salary-dot {
    background: #71927E;
}

.employee-report-panel {
    margin-bottom: 15px;
}

.table-panel-heading {
    min-height: 73px;
}

.table-heading-icon {
    width: 38px;
    height: 38px;
}

.table-heading-icon svg {
    width: 20px;
    height: 20px;
}

.report-count {
    margin-left: auto;
    padding: 6px 10px;
    border: 1px solid #DFE6EE;
    border-radius: 5px;
    background: #F3F6F9;
    color: #566A81;
    font-size: 10px;
    font-weight: 750;
    white-space: nowrap;
}

.report-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.report-table {
    width: 100%;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    color: #425267;
    font-size: 12px;
}

.report-table thead th {
    padding: 14px 15px;
    border-bottom: 1px solid #DCE3EB;
    background: #F0F3F7;
    color: #53657B;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .45px;
    text-align: left;
    text-transform: uppercase;
    white-space: nowrap;
}

.report-table thead th:first-child {
    padding-left: 21px;
}

.report-table tbody td {
    padding: 15px;
    border-bottom: 1px solid #EDF0F4;
    background: #FFFFFF;
    color: #425267;
    vertical-align: middle;
}

.report-table tbody tr:last-child td {
    border-bottom: none;
}

.report-table tbody tr {
    transition: background .15s ease;
}

.report-table tbody tr:hover td {
    background: #F7F9FC;
}

.report-table .number-column {
    width: 48px;
}

.report-table tbody td:first-child {
    padding-left: 21px;
}

.row-number {
    color: #8996A6;
    font-size: 11px;
    font-weight: 600;
}

.employee-code-badge {
    display: inline-block;
    padding: 6px 8px;
    border: 1px solid #DCE4ED;
    border-radius: 5px;
    background: #F2F5F8;
    color: #40546C;
    font-family: Consolas, "Courier New", monospace;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}

.employee-name-cell {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 150px;
}

.employee-avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 33px;
    height: 33px;
    flex-shrink: 0;
    border: 1px solid #D9E2EC;
    border-radius: 8px;
    background: #EAF0F6;
    color: #334E6B;
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
}

.employee-name {
    color: #26384D;
    font-size: 12px;
    font-weight: 750;
    overflow-wrap: anywhere;
}

.employee-email {
    color: #526E8C;
    text-decoration: none;
    overflow-wrap: anywhere;
}

.employee-email:hover {
    color: #172437;
    text-decoration: underline;
}

.designation-text {
    color: #536477;
    white-space: nowrap;
}

.department-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 9px;
    border: 1px solid #DCE5EF;
    border-radius: 5px;
    background: #F1F5F9;
    color: #425B77;
    font-size: 11px;
    font-weight: 650;
    white-space: nowrap;
}

.department-dot {
    width: 6px;
    height: 6px;
    background: #7189A4;
}

.unassigned-label {
    color: #929DAC;
    font-size: 11px;
    font-style: italic;
}

.report-empty-state {
    display: flex;
    align-items: center;
    flex-direction: column;
    padding: 48px 22px;
    text-align: center;
}

.empty-state-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 57px;
    height: 57px;
    margin-bottom: 17px;
    border: 1px solid #DEE5ED;
    border-radius: 13px;
    background: #F1F4F8;
    color: #637A95;
}

.empty-state-icon svg {
    width: 26px;
    height: 26px;
}

.report-empty-state h3 {
    margin: 0 0 8px;
    color: #27394E;
    font-size: 16px;
    font-weight: 750;
}

.report-empty-state p {
    max-width: 400px;
    margin: 0 0 19px;
    color: #8491A1;
    font-size: 12px;
    line-height: 1.7;
}

.report-page-note {
    display: flex;
    align-items: center;
    gap: 7px;
    margin: 13px 2px 0;
    color: #8995A4;
    font-size: 10px;
    line-height: 1.6;
}

.report-page-note span {
    color: #657B94;
    font-size: 13px;
}

@media (max-width: 1100px) {
    .report-filter-grid {
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    }

    .report-filter-actions {
        grid-column: 1 / -1;
    }

    .report-filter-actions .report-btn-primary {
        flex: 1;
    }
}

@media (max-width: 767px) {
    .employee-reports-page {
        padding: 15px;
    }

    .reports-header {
        align-items: stretch;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 21px;
    }

    .report-export-btn {
        align-self: flex-start;
    }

    .report-panel-heading {
        padding: 15px;
    }

    .report-panel-body {
        padding: 16px;
    }

    .report-filter-grid {
        grid-template-columns: minmax(0, 1fr);
        gap: 15px;
    }

    .report-filter-actions {
        grid-column: auto;
    }

    .report-filter-actions .report-btn-primary {
        flex: 1;
    }

    .report-summary-grid {
        grid-template-columns: minmax(0, 1fr);
        gap: 14px;
    }

    .report-summary-card {
        padding: 19px;
    }

    .summary-top h2 {
        font-size: 30px;
    }

    .summary-top h2.salary-total {
        font-size: 25px;
    }

    .report-count {
        font-size: 9px;
    }

    .report-table {
        min-width: 800px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .employee-reports-page *,
    .employee-reports-page *::before,
    .employee-reports-page *::after {
        scroll-behavior: auto !important;
        transition-duration: .01ms !important;
        animation-duration: .01ms !important;
    }
}
/* Force Apply Filters and Clear to remain visible */
.employee-reports-page .report-export-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: relative !important;
    z-index: 1 !important;
    flex-shrink: 0 !important;
}

.employee-reports-page .report-filter-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 1.4fr) minmax(180px, 0.9fr) auto !important;
    align-items: end !important;
    gap: 16px !important;
}

.employee-reports-page .report-filter-actions {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: flex-start !important;
    gap: 8px !important;
    min-width: 170px !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.employee-reports-page .report-filter-actions .report-btn-primary {
    display: inline-flex !important;
    visibility: visible !important;
    opacity: 1 !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 105px !important;
    min-height: 41px !important;
    padding: 0 12px !important;
    background: #202F43 !important;
    color: #FFFFFF !important;
    border: 1px solid #202F43 !important;
    flex-shrink: 0 !important;
}

.employee-reports-page .report-filter-actions .report-btn-secondary {
    display: inline-flex !important;
    visibility: visible !important;
    opacity: 1 !important;
    min-height: 41px !important;
    align-items: center !important;
    justify-content: center !important;
}

@media (max-width: 900px) {
    .employee-reports-page .report-filter-grid {
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
    }

    .employee-reports-page .report-filter-actions {
        grid-column: 1 / -1 !important;
    }
}

@media (max-width: 520px) {
    .employee-reports-page .report-filter-grid {
        grid-template-columns: minmax(0, 1fr) !important;
    }

    .employee-reports-page .report-filter-actions {
        grid-column: auto !important;
    }
}
/* Fix Export CSV button visibility */
.employee-reports-page a.report-export-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 9px !important;
    min-width: 125px !important;
    min-height: 42px !important;
    padding: 10px 18px !important;

    background-color: #202F43 !important;
    color: #FFFFFF !important;
    border: 1px solid #202F43 !important;
    border-radius: 7px !important;

    font-size: 12px !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    opacity: 1 !important;
    visibility: visible !important;
    position: relative !important;
    z-index: 2 !important;
}

.employee-reports-page a.report-export-btn:hover {
    background-color: #172437 !important;
    border-color: #172437 !important;
    color: #FFFFFF !important;
}
/* Table wrapper */
.employee-page-container .employee-table-wrapper {
    overflow-x: auto;
    border-top: 1px solid #E0E6ED;
}

/* Table layout */
.employee-page-container .employee-table {
    width: 100%;
    min-width: 950px;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
    color: #263548;
    font-size: 13px;
}

/* Column headings */
.employee-page-container .employee-table thead th {
    padding: 13px 16px;
    background: #F1F4F8;
    color: #53677F;
    border-bottom: 1px solid #DCE4ED;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.7px;
    text-transform: uppercase;
    white-space: nowrap;
    vertical-align: middle;
}

/* Table cells */
.employee-page-container .employee-table tbody td {
    padding: 14px 16px;
    background: #FFFFFF;
    border-bottom: 1px solid #E8EDF2;
    color: #53677F;
    vertical-align: middle;
    line-height: 1.5;
}

/* Row hover */
.employee-page-container .employee-table tbody tr:hover td {
    background: #F5F7FA;
}

/* Remove the final row's bottom border */
.employee-page-container .employee-table tbody tr:last-child td {
    border-bottom: 0;
}

/* Employee name */
.employee-page-container .employee-table .fw-semibold {
    color: #202F43 !important;
    font-weight: 700;
}

/* Email links */
.employee-page-container .employee-table a.text-decoration-none {
    color: #53677F;
    text-decoration: none;
}

.employee-page-container .employee-table a.text-decoration-none:hover {
    color: #172437;
}

/* Employee code badge */
.employee-page-container .employee-table .badge.bg-light {
    padding: 5px 8px;
    background: #F1F4F8 !important;
    border: 1px solid #DFE6EE !important;
    border-radius: 5px;
    color: #53677F !important;
    font-size: 11px;
    font-weight: 700;
}

/* Action buttons */
.employee-page-container .employee-table .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 30px;
    min-height: 30px;
    border-radius: 6px;
}

/* Responsive table */
@media (max-width: 767px) {
    .employee-page-container {
        padding: 15px !important;
    }

    .employee-page-container .employee-table thead th,
    .employee-page-container .employee-table tbody td {
        padding: 12px;
    }
}

/* Employee Report table — match the reference table */
.employee-reports-page .report-table thead th {
    background: #202A36 !important;
    color: #FFFFFF !important;
    border-bottom: 1px solid #354150 !important;
    padding: 13px 16px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.4px;
    text-transform: uppercase;
    vertical-align: middle;
    white-space: nowrap;
}

.employee-reports-page .report-table tbody td {
    background: #FFFFFF !important;
    color: #53677F;
    border-bottom: 1px solid #E8EDF2;
    padding: 14px 16px;
    vertical-align: middle;
}

.employee-reports-page .report-table tbody tr:hover td {
    background: #F5F7FA !important;
}

.employee-reports-page .report-table tbody tr:last-child td {
    border-bottom: none;
}
@endpush

@endsection
