@extends('layouts.app')

@section('title', 'Edit Department')

@section('content')
<style>
/* ==========================================
   Edit Department — Graphite + Steel
========================================== */
.department-page {
    width: 100%;
    max-width: 1100px;
    margin: 0 auto;
    color: #263447;
}

.department-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
}

.department-page .page-header h1 {
    margin: 0 0 6px;
    color: #263447;
    font-size: 27px;
    font-weight: 750;
    letter-spacing: -0.5px;
}

.department-page .page-header p,
.department-page .form-description {
    color: #718096;
    margin: 0;
    font-size: 13px;
    line-height: 1.6;
}

.department-page .department-card {
    padding: 28px;
    background: #FFFFFF;
    border: 1px solid #DFE5EC;
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(23, 36, 55, 0.045);
}

.department-page .department-card h2 {
    display: inline-block;
    padding: 10px 14px;
    margin: 0 0 16px;
    background: #FFFFFF;
    border: 1px solid #DFE5EC;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(23, 36, 55, 0.06);
    color: #263447;
    font-size: 18px;
    font-weight: 700;
    letter-spacing: -0.2px;
}

.department-page .form-description {
    margin-bottom: 25px;
}

.department-page .form-group {
    margin-bottom: 22px;
}

.department-page .form-group label {
    display: block;
    margin-bottom: 7px;
    color: #475569;
    font-size: 13px;
    font-weight: 650;
}

.department-page .form-group label span {
    color: #C2414B;
}

.department-page .form-group input,
.department-page .form-group textarea {
    display: block;
    box-sizing: border-box;
    width: 100%;
    padding: 11px 13px;
    border: 1px solid #D6DEE8;
    border-radius: 7px;
    background: #FFFFFF;
    color: #263447;
    font-family: inherit;
    font-size: 13px;
    box-shadow: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.department-page .form-group input {
    min-height: 43px;
}

.department-page .form-group textarea {
    min-height: 115px;
    resize: vertical;
}

.department-page .form-group input:focus,
.department-page .form-group textarea:focus {
    outline: none;
    border-color: #7188A5;
    box-shadow: 0 0 0 3px rgba(113, 136, 165, 0.14);
}

.department-page .form-group .input-error {
    border-color: #D66A73;
}

.department-page .form-group .input-error:focus {
    box-shadow: 0 0 0 3px rgba(214, 106, 115, 0.12);
}

.department-page .error-message {
    display: block;
    margin-top: 6px;
    color: #B42332;
    font-size: 12px;
}

.department-page .validation-errors {
    padding: 15px 18px;
    margin-bottom: 20px;
    background: #FFF1F1;
    border: 1px solid #F2CDCD;
    border-radius: 8px;
    color: #982D35;
    font-size: 13px;
}

.department-page .validation-errors ul {
    margin: 8px 0 0;
    padding-left: 20px;
}

.department-page .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 40px;
    padding: 9px 16px;
    border: 1px solid transparent;
    border-radius: 7px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 650;
    line-height: 1.4;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
}

.department-page .btn-primary {
    background: #26384D;
    border-color: #26384D;
    color: #FFFFFF;
}

.department-page .btn-primary:hover {
    background: #344B64;
    border-color: #344B64;
    box-shadow: 0 3px 8px rgba(23, 36, 55, 0.12);
    transform: translateY(-1px);
}

.department-page .btn-secondary {
    background: #FFFFFF;
    color: #475569;
    border-color: #D3DBE5;
}

.department-page .btn-secondary:hover {
    background: #EDF1F5;
    border-color: #C2CCD8;
    color: #263447;
}

.department-page .btn:focus-visible {
    outline: 2px solid #7188A5;
    outline-offset: 3px;
}

.department-page .form-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    padding-top: 18px;
    border-top: 1px solid #E8EDF3;
}

@media (max-width: 640px) {
    .department-page .page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .department-page .page-header h1 {
        font-size: 23px;
    }

    .department-page .department-card {
        padding: 20px;
    }

    .department-page .form-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .department-page .form-actions .btn {
        width: 100%;
        box-sizing: border-box;
    }
}

@media (prefers-reduced-motion: reduce) {
    .department-page *,
    .department-page *::before,
    .department-page *::after {
        transition: none !important;
    }
}
</style>
<div class="container-fluid department-page">

    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h1 class="mb-1">Edit Department</h1>
            <p class="text-muted mb-0">
                Update the department details below.
            </p>
        </div>

        <a href="{{ route('admin.departments.index') }}"
              class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Departments
        </a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="validation-errors" role="alert">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Edit Department Form --}}
    <div class="department-card">
        <h2>Department Information</h2>
            <form method="POST"
                  action="{{ route('admin.departments.update', $department) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="form-label">
                        Department Name <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="@error('name') input-error @enderror"
                        value="{{ old('name', $department->name) }}"
                        maxlength="255"
                        required
                    >

                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        class="@error('description') input-error @enderror"
                        rows="4"
                    >{{ old('description', $department->description) }}</textarea>

                    @error('description')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Save Changes
                    </button>

                    <a href="{{ route('admin.departments.show', $department) }}"
                              class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
    </div>

</div>
@endsection
