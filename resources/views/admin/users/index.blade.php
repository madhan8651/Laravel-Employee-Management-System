@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid py-3 employee-page-container">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">User Management</h1>
            <p class="text-secondary mb-0">
                Manage user accounts and system access.
            </p>
        </div>

        <a href="{{ route('admin.users.create') }}"
           class="btn btn-primary d-inline-flex align-items-center justify-content-center text-nowrap">
            <i class="bi bi-person-plus me-2"></i> Add User
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

    {{-- User Directory Card --}}
    <div class="card border-0 shadow-sm rounded-3 employee-directory-card">

        {{-- Card Header --}}
        <div class="card-header bg-white border-bottom py-3">
            <h2 class="h6 fw-bold mb-0">
                <i class="bi bi-people me-2 text-primary"></i>
                User Directory
            </h2>
        </div>

        {{-- Empty State --}}
        @if ($users->isEmpty())
            <div class="card-body text-center py-5">
                <i class="bi bi-people display-5 text-secondary"></i>

                <h2 class="h5 fw-bold mt-3">No users found</h2>

                <p class="text-secondary mb-3">
                    No user accounts are available yet.
                </p>

                <a href="{{ route('admin.users.create') }}"
                   class="btn btn-primary btn-sm">
                    <i class="bi bi-person-plus me-1"></i>
                    Add First User
                </a>
            </div>
        @else

            {{-- User Table --}}
            <div class="table-responsive employee-table-wrapper">
                <table class="table table-hover align-middle mb-0 employee-table">

                    <thead>
                        <tr>
                            <th scope="col" class="px-3">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Date Created</th>
                            <th scope="col" class="text-end text-nowrap pe-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                {{-- Row Number --}}
                                <td class="px-3 text-secondary">
                                    {{ $users instanceof \Illuminate\Contracts\Pagination\Paginator
                                        || $users instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator
                                        ? $users->firstItem() + $loop->index
                                        : $loop->iteration }}
                                </td>

                                {{-- User Name --}}
                                <td class="text-nowrap">
                                    <span class="fw-semibold text-dark">
                                        {{ $user->name }}
                                    </span>

                                    @if (auth()->id() === $user->id)
                                        <span class="badge bg-success ms-1">
                                            You
                                        </span>
                                    @endif
                                </td>

                                {{-- Email --}}
                                <td>
                                    <a href="mailto:{{ $user->email }}"
                                       class="text-decoration-none">
                                        {{ $user->email }}
                                    </a>
                                </td>

                                {{-- Date Created --}}
                                <td class="text-secondary text-nowrap">
                                    {{ $user->created_at?->format('d M Y') ?? '—' }}
                                </td>

                                {{-- Actions --}}
                                <td class="text-end text-nowrap pe-3">
                                    <div class="d-inline-flex align-items-center gap-1 flex-nowrap">

                                        {{-- View --}}
                                        <a href="{{ route('admin.users.show', $user) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="View User"
                                           aria-label="View User">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                                <path d="M8 3.5c-3.5 0-6.3 3.4-7.3 4.5 1 1.1 3.8 4.5 7.3 4.5s6.3-3.4 7.3-4.5C14.3 6.9 11.5 3.5 8 3.5Zm0 7.5a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>
                                            </svg>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Edit User"
                                           aria-label="Edit User">
                                               <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                                   <path d="M13.5 1a1.5 1.5 0 0 1 1.06 2.56l-8.8 8.8-3.4.85.85-3.4 8.8-8.8A1.5 1.5 0 0 1 13.5 1Z"/>
                                               </svg>
                                        </a>

                                        {{-- Delete --}}
                                        @if (auth()->id() !== $user->id)
                                            <form method="POST"
                                                  action="{{ route('admin.users.destroy', $user) }}"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this user?')">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Delete User"
                                                        aria-label="Delete User">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                                        <path d="M5.5 1a1 1 0 0 0-1 1v1H2v1.5h12V3h-2.5V2a1 1 0 0 0-1-1h-5ZM3.5 5h9l-.6 9.1a1 1 0 0 1-1 .9H5.1a1 1 0 0 1-1-.9L3.5 5Z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-light text-secondary border">
                                                Current Account
                                            </span>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($users instanceof \Illuminate\Pagination\AbstractPaginator && $users->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            @endif

        @endif

    </div>
</div>
@endsection