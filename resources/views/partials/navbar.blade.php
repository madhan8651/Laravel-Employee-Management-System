<nav class="navbar">
    <a class="navbar-brand" href="{{ route('dashboard') }}">
        <span class="brand-mark">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path fill="currentColor" d="M3 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v3h4a2 2 0 0 1 2 2v11h-8v-4h-3v4H3Zm3-14h2V5H6v2Zm4 0h2V5h-2v2ZM6 11h2V9H6v2Zm4 0h2V9h-2v2Zm-4 4h2v-2H6v2Zm4 0h2v-2h-2v2Zm7-4h2V9h-2v2Zm0 4h2v-2h-2v2Z"/>
            </svg>
        </span>

        <span class="brand-text">
            <span class="brand-title">
                {{ config('app.name', 'Employee Management System') }}
            </span>
            <span class="brand-subtitle">
                {{ auth()->user()?->role === 'admin' ? 'Administration Portal' : 'Employee Portal' }}
            </span>
        </span>
    </a>

    <div class="navbar-user">
        @auth
            @php
                $navbarUser = auth()->user();
                $navbarName = (string) $navbarUser->getAttribute('name');

                $navbarEmployee = $navbarUser->employee;

                $navbarPhotoUrl = $navbarEmployee
                    ? $navbarEmployee->getProfilePhotoUrl()
                    : $navbarUser->getProfilePhotoUrl();
            @endphp

            <span class="navbar-india-time" aria-label="Current time in India">
                <time class="navbar-india-time-value"></time>
            </span>

            @php
                $navbarNotifications = $navbarUser->notifications()
                    ->latest()
                    ->take(5)
                    ->get();

                $unreadNotificationCount = $navbarUser->unreadNotifications()->count();
            @endphp

            <div class="navbar-notifications">
                <details>
                    <summary
                        class="navbar-notification-toggle"
                        aria-label="Notifications"
                        title="Notifications"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="19"
                            height="19"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2Zm6-6v-5a6 6 0 0 0-5-5.91V4a1 1 0 1 0-2 0v1.09A6 6 0 0 0 6 11v5l-2 2v1h16v-1l-2-2Z"/>
                        </svg>

                        @if ($unreadNotificationCount > 0)
                            <span class="notification-count">
                                {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                            </span>
                        @endif
                    </summary>

                    <div class="notification-dropdown">
                        <div class="notification-dropdown-header">
                            Notifications
                        </div>

                        @forelse ($navbarNotifications as $notification)
                            <div class="notification-item {{ $notification->unread() ? 'unread' : '' }}">
                                <p>
                                    {{ $notification->getAttribute('data')['message'] ?? 'You have a new notification.' }}
                                </p>

                                <div class="notification-timestamp">
                                    <span>
                                        {{ $notification->getAttribute('created_at')->diffForHumans() }}
                                    </span>
                                </div>

                                @if ($notification->unread())
                                    <form
                                        class="notification-read-form"
                                        method="POST"
                                        action="{{ route('notifications.read', $notification->getKey()) }}"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="notification-read-button"
                                        >
                                            Mark as read
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="notification-empty">
                                You're all caught up!
                            </div>
                        @endforelse
                    </div>
                </details>
            </div>

            <a href="{{ route('employee.profile.show') }}"
               class="navbar-profile-link"
               title="Open My Profile"
               aria-label="Open My Profile">
                @if ($navbarPhotoUrl)
                    <img
                        class="navbar-avatar navbar-avatar-image"
                        src="{{ $navbarPhotoUrl }}"
                        alt="{{ $navbarName }}'s profile photo"
                        width="42"
                        height="42"
                        onerror="this.style.display='none'; this.nextElementSibling.hidden=false;"
                    >
                    <span class="navbar-avatar" hidden aria-hidden="true">
                        {{ strtoupper(substr($navbarName, 0, 1)) }}
                    </span>
                @else
                    <span class="navbar-avatar" aria-label="No profile photo uploaded">
                        {{ strtoupper(substr($navbarName, 0, 1)) }}
                    </span>
                @endif
            </a>

            <span class="navbar-profile-arrow" aria-hidden="true">
                <i class="bi bi-chevron-down"></i>
            </span>
        @endauth

        @guest
            @if (!request()->routeIs('password.reset'))
                <a class="navbar-login" href="{{ route('login') }}">
                    Sign in <i class="bi bi-arrow-right"></i>
                </a>
            @endif
        @endguest
    </div>
</nav>

<style>
    .navbar-notification-toggle svg {
        display: block;
        width: 19px;
        height: 19px;
        color: #35465e;
        flex-shrink: 0;
    }
</style>

<script>
    (() => {
        const clocks = document.querySelectorAll('.navbar-india-time-value');
        const formatter = new Intl.DateTimeFormat('en-IN', {
            timeZone: 'Asia/Kolkata',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        });

        const updateClocks = () => {
            const now = new Date();
            clocks.forEach((clock) => {
                clock.textContent = formatter.format(now);
                clock.dateTime = now.toISOString();
            });
        };

        updateClocks();
        window.setInterval(updateClocks, 1000);
    })();
</script>
