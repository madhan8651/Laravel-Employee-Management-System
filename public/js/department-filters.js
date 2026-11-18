document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('department-filter-form');
    const tableContent = document.getElementById('department-table-content');
    const loading = document.getElementById('department-loading');

    if (!filterForm || !tableContent) {
        return;
    }

    filterForm.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(button => {
        const label = (button.textContent || button.value || '').trim().toLowerCase();

        if (label === 'apply') {
            button.remove();
        }
    });

    let debounceTimer;

    function fetchDepartments(url = null) {
        const requestUrl = url || new URL(filterForm.action, window.location.origin);

        if (!url) {
            const formData = new FormData(filterForm);

            requestUrl.search = new URLSearchParams(formData).toString();
        }

        const currentUrl = requestUrl.toString();

        if (loading) {
            loading.classList.remove('d-none');
        }

        fetch(currentUrl, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Unable to load departments.');
            }

            return response.json();
        })
        .then(data => {
            tableContent.innerHTML = data.html;

            const historyUrl = new URL(currentUrl);

            historyUrl.searchParams.delete('reset');

            window.history.pushState({}, '', historyUrl);
        })
        .catch(error => {
            console.error('Department AJAX error:', error);
        })
        .finally(() => {
            if (loading) {
                loading.classList.add('d-none');
            }
        });
    }

    // Search with debounce.
    const searchInput = filterForm.querySelector('[name="search"]');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);

            debounceTimer = setTimeout(function () {
                fetchDepartments();
            }, 400);
        });
    }

    // Status filter.
    const statusInput = filterForm.querySelector('[name="status"]');

    if (statusInput) {
        statusInput.addEventListener('change', function () {
            fetchDepartments();
        });
    }

    // Prevent normal form submission.
    filterForm.addEventListener('submit', function (event) {
        event.preventDefault();

        clearTimeout(debounceTimer);
        fetchDepartments();
    });

    // Reset filters using AJAX.
    const resetButton = document.getElementById('department-filter-reset');

    if (resetButton) {
        resetButton.addEventListener('click', function (event) {
            event.preventDefault();

            clearTimeout(debounceTimer);

            // Clear the filter fields.
            filterForm.querySelector('[name="search"]').value = '';
            filterForm.querySelector('[name="status"]').value = '';

            // Request the controller to clear session filters.
            fetchDepartments(filterForm.dataset.resetUrl);
        });
    }

    // AJAX pagination.
    tableContent.addEventListener('click', function (event) {
        const paginationLink = event.target.closest('.pagination a');

        if (!paginationLink) {
            return;
        }

        event.preventDefault();

        fetchDepartments(paginationLink.href);
    });

    // Browser back and forward.
    window.addEventListener('popstate', function () {
        fetchDepartments(window.location.href);
    });
});
