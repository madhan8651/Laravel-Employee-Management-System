<aside class="sidebar">
    <p class="sidebar-heading">{{ __('navigation.workspace') }}</p>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}"
           class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>{{ __('navigation.dashboard') }}</span>
        </a>

        @if (auth()->user()->role === \App\Enums\Role::Admin)
            <a href="{{ route('admin.employees.index') }}"
               class="{{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>{{ __('navigation.employees') }}</span>
            </a>

            <a href="{{ route('admin.departments.index') }}"
               class="{{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3-fill"></i>
                <span>{{ __('navigation.departments') }}</span>
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i>
                <span>{{ __('navigation.users') }}</span>
            </a>

            <a href="{{ route('admin.reports.index') }}"
               class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line-fill"></i>
                <span>{{ __('navigation.reports') }}</span>
            </a>
        @endif
    </nav>
</aside>