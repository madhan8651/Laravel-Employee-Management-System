@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="container-fluid py-3">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Departments</h1>
            <p class="text-secondary mb-0">
                Manage your departments and team structure.
            </p>
        </div>

        <a href="{{ route('admin.departments.create') }}"
           class="btn btn-primary"
           title="Add Department"
           aria-label="Add Department">
            <i class="bi bi-plus-circle me-1"></i>
            Add Department
        </a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please fix the following errors:</strong>

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

    {{-- Department Search and Filters --}}
    <form method="GET"
          action="{{ route('admin.departments.index') }}"
            id="department-filter-form"
            data-reset-url="{{ route('admin.departments.index', ['reset' => 1]) }}"
          class="card border-0 shadow-sm rounded-3 mb-4">

        <div class="card-body">
            <div class="row g-3 align-items-end">

                <div class="col-md-6">
                    <label for="search" class="form-label">
                        Search Departments
                    </label>

                    <input
                        type="text"
                        id="search"
                        name="search"
                        class="form-control"
                        placeholder="Department name or description"
                        value="{{ $filters['search'] ?? '' }}"
                    >
                </div>

                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>

                    <select id="status"
                            name="status"
                            class="form-select">
                        <option value="">All statuses</option>

                        <option value="active"
                            @selected(($filters['status'] ?? '') === 'active')>
                            Active
                        </option>

                        <option value="inactive"
                            @selected(($filters['status'] ?? '') === 'inactive')>
                            Inactive
                        </option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit"
                            class="btn btn-primary"
                            title="Apply Filters"
                            aria-label="Apply Filters">
                        <i class="bi bi-search me-1"></i>
                        Apply
                    </button>

                    <a href="{{ route('admin.departments.index', ['reset' => 1]) }}"
                              id="department-filter-reset"
                       class="btn btn-primary"
                       title="Reset Filters"
                       aria-label="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </a>
                </div>

            </div>
        </div>
    </form>

    <div id="department-loading" class="d-none text-center py-2">
        <span class="spinner-border spinner-border-sm text-primary"
              role="status"></span>
        <span class="ms-2 text-secondary">Loading departments...</span>
    </div>

    {{-- Department Directory --}}
    <div class="card border-0 shadow-sm rounded-3"
         id="department-table-container">

        <div class="card-header bg-white border-bottom py-3">
            <h2 class="h6 fw-bold mb-0">
                <i class="bi bi-building me-2"></i>
                Department Directory
            </h2>
        </div>

        <div id="department-table-content">
            @include('admin.departments.partials.table')
        </div>

    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/department-filters.js') }}" defer></script>
@endpush
