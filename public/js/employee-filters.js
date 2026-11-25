
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('employee-filter-form');
    const tableBody = document.getElementById('employee-table-body');
    const tableContainer = document.getElementById('employee-table-container');
    const emptyState = document.getElementById('employee-empty-state');
    const paginationContainer = document.getElementById('employee-pagination');
    const loadingIndicator = document.getElementById('employee-loading');

    if (!form || !tableBody || !tableContainer) {
        return;
    }

    const searchInput = document.getElementById('search');
    const departmentInput = document.getElementById('department_id');
    const statusInput = document.getElementById('status');
    const genderInput = document.getElementById('gender');
    const sortInput = document.getElementById('sort');
    const directionInput = document.getElementById('direction');

    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    )?.content;

    let searchTimer = null;
    let activeController = null;

    // Safely escape values before inserting them into HTML.
    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, character => {
            const entities = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };

            return entities[character];
        });
    }

    // Render action icons using inline SVG.
    function iconSvg(name) {
        const icons = {
            eye: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 3.5c-3.5 0-6.3 3.4-7.3 4.5 1 1.1 3.8 4.5 7.3 4.5s6.3-3.4 7.3-4.5C14.3 6.9 11.5 3.5 8 3.5Zm0 7.5a3 3 0 1 1 0-6 3 3 0 0 1 0 6Zm0-1.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/></svg>',

            edit: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M13.5 1a1.5 1.5 0 0 1 1.06 2.56l-8.8 8.8-3.4.85.85-3.4 8.8-8.8A1.5 1.5 0 0 1 13.5 1Z"/></svg>',

            toggleOn: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M5 3h6a5 5 0 0 1 0 10H5a5 5 0 0 1 0-10Z"/><circle cx="11" cy="8" r="2.5"/></svg>',

            toggleOff: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M5 3h6a5 5 0 0 1 0 10H5a5 5 0 0 1 0-10Z"/><circle cx="5" cy="8" r="2.5"/></svg>'
        };

        return icons[name] || '';
    }

    // Generate employee action URLs using Laravel route templates.
    function employeeUrl(attribute, employeeId) {
        const template = tableContainer.dataset[attribute];

        if (!template) {
            return '#';
        }

        return template.replace('__ID__', encodeURIComponent(employeeId));
    }

    // Generate a CSRF-protected employee action form.
    function actionForm(url, method, icon, buttonClass, title, confirmation) {
        return `
            <form method="POST"
                  action="${escapeHtml(url)}"
                  class="d-inline employee-action-form"
                  data-confirm="${escapeHtml(confirmation)}">

                <input type="hidden"
                       name="_token"
                       value="${escapeHtml(csrfToken)}">

                <input type="hidden"
                       name="_method"
                       value="${method}">

                <button type="submit"
                        class="btn btn-sm ${buttonClass}"
                        title="${escapeHtml(title)}"
                        aria-label="${escapeHtml(title)}">
                    ${iconSvg(icon)}
                </button>
            </form>
        `;
    }

    // Render employee action buttons according to API permissions.
    function renderActions(employee) {
        const permissions = employee.permissions || {};
        const id = employee.id;
        const status = employee.status;

        let html = '<div class="department-actions">';

        if (permissions.view) {
            html += `
                <a href="${escapeHtml(employeeUrl('viewUrl', id))}"
                   class="btn btn-sm btn-outline-primary"
                   title="View Employee"
                   aria-label="View Employee">
                    ${iconSvg('eye')}
                </a>
            `;
        }

        if (permissions.update) {
            html += `
                <a href="${escapeHtml(employeeUrl('editUrl', id))}"
                   class="btn btn-sm btn-outline-secondary"
                   title="Edit Employee"
                   aria-label="Edit Employee">
                    ${iconSvg('edit')}
                </a>
            `;
        }

        if (permissions.update && status !== 'terminated') {
            const isActive = status === 'active';

            html += actionForm(
                employeeUrl('toggleUrl', id),
                'PATCH',
                isActive ? 'toggleOn' : 'toggleOff',
                isActive ? 'btn-outline-warning' : 'btn-outline-success',
                isActive ? 'Deactivate employee' : 'Activate employee',
                `Are you sure you want to ${isActive ? 'deactivate' : 'activate'} this employee?`
            );
        }

        html += '</div>';

        return html;
    }

    // Render a single employee row.
    function renderEmployee(employee, index, meta) {
        const status = employee.status;
        const gender = employee.gender;

        const statusBadges = {
            active: '<span class="badge bg-success">Active</span>',
            inactive: '<span class="badge bg-secondary">Inactive</span>',
            terminated: '<span class="badge bg-danger">Terminated</span>'
        };

        const genderLabels = {
            male: 'Male',
            female: 'Female',
            other: 'Other'
        };

        const rowNumber =
            ((meta.current_page || 1) - 1) * (meta.per_page || 10) +
            index + 1;

        return `
            <tr>
                <td class="px-3 text-secondary">
                    ${rowNumber}
                </td>

                <td class="text-nowrap">
                    <span class="badge bg-light text-dark border">
                        ${escapeHtml(employee.employee_code)}
                    </span>
                </td>

                <td class="text-nowrap">
                    <span class="fw-semibold text-dark">
                        ${escapeHtml(employee.name)}
                    </span>
                </td>

                <td>
                    <a href="mailto:${escapeHtml(employee.email)}"
                       class="text-decoration-none">
                        ${escapeHtml(employee.email)}
                    </a>
                </td>

                <td>
                    ${escapeHtml(employee.designation || '—')}
                </td>

                <td>
                    ${
                        employee.department
                            ? `<span class="badge bg-primary-subtle text-primary">
                                    ${escapeHtml(employee.department.name)}
                               </span>`
                            : '<span class="text-secondary">Unassigned</span>'
                    }
                </td>

                <td class="text-nowrap">
                    ${
                        genderLabels[gender] ||
                        '<span class="text-secondary">Not specified</span>'
                    }
                </td>

                <td class="text-nowrap">
                    ${statusBadges[status] ||
                      '<span class="badge bg-light text-dark">Not set</span>'}
                </td>

                <td class="text-nowrap pe-3">
                    ${renderActions(employee)}
                </td>
            </tr>
        `;
    }

    // Render pagination controls.
    function renderPagination(meta) {
        const currentPage = Number(meta.current_page || 1);
        const lastPage = Number(meta.last_page || 1);
        const total = Number(meta.total || 0);
        const from = meta.from ?? 0;
        const to = meta.to ?? 0;

        if (lastPage <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }

        let html = `
            <div class="d-flex flex-column flex-md-row
                        justify-content-between align-items-center gap-3">

                <small class="text-secondary">
                    Showing ${from} to ${to} of ${total} employees
                </small>

                <nav aria-label="Employee pagination">
                    <ul class="pagination pagination-sm mb-0">
        `;

        html += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link"
                        type="button"
                        data-page="${currentPage - 1}"
                        ${currentPage === 1 ? 'disabled' : ''}>
                    Previous
                </button>
            </li>
        `;

        for (let page = 1; page <= lastPage; page++) {
            html += `
                <li class="page-item ${page === currentPage ? 'active' : ''}">
                    <button class="page-link"
                            type="button"
                            data-page="${page}">
                        ${page}
                    </button>
                </li>
            `;
        }

        html += `
            <li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
                <button class="page-link"
                        type="button"
                        data-page="${currentPage + 1}"
                        ${currentPage === lastPage ? 'disabled' : ''}>
                    Next
                </button>
            </li>
        `;

        html += `
                    </ul>
                </nav>
            </div>
        `;

        paginationContainer.innerHTML = html;
    }

    // Show API errors without replacing the employee table.
    function showError(message) {
        let errorElement = document.getElementById('employee-ajax-error');

        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.id = 'employee-ajax-error';
            errorElement.className = 'alert alert-danger mx-3 mt-3';
            errorElement.setAttribute('role', 'alert');

            form.closest('.employee-directory-card')
                .insertBefore(errorElement, form.closest('.employee-filter-panel'));
        }

        errorElement.textContent = message;
    }

    function clearError() {
        document.getElementById('employee-ajax-error')?.remove();
    }

    // Read the current filter values.
    function getFilters() {
        return {
            search: searchInput.value.trim(),
            department_id: departmentInput.value,
            status: statusInput.value,
            gender: genderInput.value,
            sort: sortInput.value,
            direction: directionInput.value
        };
    }

    // Fetch employees from the REST API.
    async function loadEmployees(page = 1) {
        if (activeController) {
            activeController.abort();
        }

        activeController = new AbortController();

        const controller = activeController;

        const params = new URLSearchParams();

        Object.entries(getFilters()).forEach(([key, value]) => {
            if (value !== '') {
                params.set(key, value);
            }
        });

        params.set('page', page);
        params.set('per_page', 10);

        loadingIndicator.style.display = 'block';
        clearError();

        try {
            const response = await fetch(
                `/api/employees?${params.toString()}`,
                {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    signal: controller.signal
                }
            );

            if (!response.ok) {
                if (response.status === 401) {
                    throw new Error('Your session has expired. Please log in again.');
                }

                if (response.status === 403) {
                    throw new Error('You are not authorized to view these employees.');
                }

                if (response.status === 422) {
                    throw new Error('Invalid filter values. Please check your filters.');
                }

                throw new Error(`Unable to load employees. HTTP ${response.status}`);
            }

            const result = await response.json();

            if (!Array.isArray(result.data)) {
                throw new Error('Unexpected employee API response.');
            }

            const employees = result.data;
            const meta = result.meta || {};

            tableBody.innerHTML = employees
                .map((employee, index) =>
                    renderEmployee(employee, index, meta)
                )
                .join('');

            const hasEmployees = employees.length > 0;

            tableContainer.style.display = hasEmployees ? '' : 'none';
            emptyState.style.display = hasEmployees ? 'none' : '';

            renderPagination(meta);

        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            console.error('Employee AJAX error:', error);
            showError(error.message || 'Something went wrong while loading employees.');

        } finally {
            if (activeController === controller) {
                loadingIndicator.style.display = 'none';
            }
        }
    }

    // Search after the user pauses typing.
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);

        searchTimer = setTimeout(() => {
            loadEmployees(1);
        }, 400);
    });

    // Apply other filters immediately.
    [
        departmentInput,
        statusInput,
        genderInput,
        sortInput,
        directionInput
    ].forEach(input => {
        input.addEventListener('change', () => {
            loadEmployees(1);
        });
    });

    // Prevent normal form submission and use AJAX.
    form.addEventListener('submit', event => {
        event.preventDefault();

        clearTimeout(searchTimer);
        loadEmployees(1);
    });

    // Handle pagination clicks.
    paginationContainer.addEventListener('click', event => {
        const button = event.target.closest('[data-page]');

        if (!button || button.disabled) {
            return;
        }

        const page = Number(button.dataset.page);

        if (page > 0) {
            loadEmployees(page);
        }
    });

    // Confirm existing employee action forms.
    tableBody.addEventListener('submit', event => {
        const actionForm = event.target.closest('.employee-action-form');

        if (!actionForm) {
            return;
        }

        const confirmation = actionForm.dataset.confirm;

        if (confirmation && !window.confirm(confirmation)) {
            event.preventDefault();
        }
    });

    // Initial API load.
    loadEmployees(1);
});
