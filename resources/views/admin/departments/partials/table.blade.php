{{-- Department Directory Content --}}

@if ($departments->isEmpty())

    {{-- Empty State --}}
    <div class="card-body text-center py-5">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
            <path d="M4 1h8v14H4V1ZM5.5 3v1.5H7V3H5.5Zm3.5 0v1.5h1.5V3H9ZM5.5 6v1.5H7V6H5.5ZM9 6v1.5h1.5V6H9ZM7 15v-3h2v3H7Z"/>
        </svg>

        <h2 class="h5 fw-bold mt-3">No departments found</h2>

        <p class="text-secondary mb-4">
            Try changing your search or filters, or create a new department.
        </p>

        <a href="{{ route('admin.departments.create') }}"
           class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1ZM7.25 4.5h1.5v2.75h2.75v1.5H8.75v2.75h-1.5V8.75H4.5v-1.5h2.75V4.5Z"/>
            </svg>
            Add Department
        </a>
    </div>

@else

    {{-- Departments Table --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th scope="col" class="px-3 py-3">#</th>
                    <th scope="col" class="py-3">Department Name</th>
                    <th scope="col" class="py-3">Description</th>
                    <th scope="col" class="py-3 text-center">Employees</th>
                    <th scope="col" class="py-3 text-end">Total Salary</th>
                    <th scope="col" class="py-3 text-end">Average Salary</th>
                    <th scope="col" class="py-3 text-end pe-3">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($departments as $department)
                    <tr>
                        {{-- Row Number --}}
                        <td class="px-3 text-secondary">
                            {{ $departments->firstItem() + $loop->index }}
                        </td>

                        {{-- Department Name --}}
                        <td>
                            <span class="fw-semibold text-dark">
                                {{ $department->name }}
                            </span>

                            <div class="mt-1">
                                @if ($department->status === 'active')
                                    <span class="badge bg-success-subtle text-success">
                                        Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Description --}}
                        <td class="text-secondary">
                            {{ $department->description ?: '—' }}
                        </td>

                        {{-- Employee Count --}}
                        <td class="text-center">
                            <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                                {{ $department->employees_count }}
                            </span>
                        </td>

                        {{-- Total Salary --}}
                        <td class="text-end text-nowrap">
                            @if ($department->employees_sum_salary !== null)
                                ₹{{ number_format((float) $department->employees_sum_salary, 2) }}
                            @else
                                <span class="text-secondary">N/A</span>
                            @endif
                        </td>

                        {{-- Average Salary --}}
                        <td class="text-end text-nowrap">
                            @if ($department->employees_avg_salary !== null)
                                ₹{{ number_format((float) $department->employees_avg_salary, 2) }}
                            @else
                                <span class="text-secondary">N/A</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="text-nowrap pe-3">
                            <div class="d-flex align-items-center justify-content-end gap-2">

                                {{-- View --}}
                                <a href="{{ route('admin.departments.show', $department) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="View Department"
                                   aria-label="View Department">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                        <path d="M8 3.5c-3.5 0-6.3 3.4-7.3 4.5 1 1.1 3.8 4.5 7.3 4.5s6.3-3.4 7.3-4.5C14.3 6.9 11.5 3.5 8 3.5Zm0 7.5a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>
                                    </svg>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('admin.departments.edit', $department) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   title="Edit Department"
                                   aria-label="Edit Department">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                        <path d="M13.5 1a1.5 1.5 0 0 1 1.06 2.56l-8.8 8.8-3.4.85.85-3.4 8.8-8.8A1.5 1.5 0 0 1 13.5 1Z"/>
                                    </svg>
                                </a>

                                {{-- Delete --}}
                                @if ($department->employees_count == 0)
                                    <form method="POST"
                                          action="{{ route('admin.departments.destroy', $department) }}"
                                          onsubmit="return confirm('Are you sure you want to delete this department?')"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete Department"
                                                aria-label="Delete Department">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                                <path d="M5.5 1a1 1 0 0 0-1 1v1H2v1.5h12V3h-2.5V2a1 1 0 0 0-1-1h-5ZM3.5 5h9l-.6 9.1a1 1 0 0 1-1 .9H5.1a1 1 0 0 1-1-.9L3.5 5Z"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis"
                                          title="This department has employees and cannot be deleted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                            <path d="M4 6V4a4 4 0 1 1 8 0v2h1v9H3V6h1Zm1.5 0h5V4a2.5 2.5 0 0 0-5 0v2Z"/>
                                        </svg>
                                        In Use
                                    </span>
                                @endif

                                {{-- Activate / Deactivate --}}
                                <form method="POST"
                                      action="{{ route('admin.departments.toggle-status', $department) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Change this department status?')">
                                    @csrf
                                    @method('PATCH')

                                    @if ($department->status === 'active')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Deactivate Department"
                                                aria-label="Deactivate Department">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                                <path d="M5 3h6a5 5 0 0 1 0 10H5a5 5 0 0 1 0-10Z"/>
                                                <circle cx="11" cy="8" r="2.5"/>
                                            </svg>
                                        </button>
                                    @else
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-success"
                                                title="Activate Department"
                                                aria-label="Activate Department">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                                <path d="M5 3h6a5 5 0 0 1 0 10H5a5 5 0 0 1 0-10Z"/>
                                                <circle cx="5" cy="8" r="2.5"/>
                                            </svg>
                                        </button>
                                    @endif
                                </form>

                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>

    {{-- Pagination --}}
    <div class="card-footer bg-white border-0 py-3">
        {{ $departments->links() }}
    </div>

@endif
