@extends('layouts.app')

@section('title', 'Add Department')

@section('content')
<div class="container-fluid px-0">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="page-title">Add Department</h1>
            <p class="page-subtitle mb-0">
                Create a department to organize your employees.
            </p>
        </div>

        <a href="{{ route('admin.departments.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Departments
        </a>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

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

    {{-- Department Form --}}
    <div class="form-card">

        {{-- Form Heading --}}
        <div class="mb-4 pb-3 border-bottom">
            <h2 class="h5 fw-bold mb-1">
                <i class="bi bi-building-add me-2 text-primary"></i>
                Department Information
            </h2>

            <p class="text-secondary mb-0">
                Enter the department details below.
                Fields marked <span class="text-danger">*</span> are required.
            </p>
        </div>

        <form method="POST"
              action="{{ route('admin.departments.store') }}">
            @csrf

            <div class="row g-4">

                {{-- Department Name --}}
                <div class="col-md-6">
                    <label for="name" class="form-label">
                        Department Name <span class="text-danger">*</span>
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="e.g. Human Resources"
                        maxlength="255"
                        autocomplete="off"
                        required
                        autofocus
                    >

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="col-12">
                    <label for="description" class="form-label">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        maxlength="1000"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Describe the department's responsibilities..."
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div class="form-text">
                        Provide a brief description of the department's responsibilities.
                    </div>
                </div>

            </div>

            {{-- Form Actions --}}
            <div class="d-flex flex-wrap gap-2 mt-4 pt-4 border-top">

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-building-add me-1"></i>
                    Create Department
                </button>

                <a href="{{ route('admin.departments.index') }}"
                   class="btn btn-light border">
                    Cancel
                </a>

            </div>
        </form>
    </div>
</div>
@endsection