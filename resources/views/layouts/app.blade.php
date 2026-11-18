<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Employee Management System'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .app-layout {
    display: flex;
    min-height: 100vh;
}

.main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.page-content {
    flex: 1;
}
        .navbar-profile-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border-radius: 50%;
    text-decoration: none;
}

.navbar-profile-link:focus-visible {
    outline: 3px solid #93c5fd;
    outline-offset: 3px;
}

.navbar-avatar-image {
    display: block;
    object-fit: cover;
    border: 2px solid #dbeafe;
    background: #eff6ff;
}

.navbar-avatar {
    overflow: hidden;
}
        :root {
            --navy-950: #0b1220;
            --navy-900: #111b2e;
            --navy-800: #192842;
            --blue-600: #2563eb;
            --blue-500: #3b82f6;
            --blue-50: #eff6ff;
            --surface: #ffffff;
            --page-bg: #f5f7fb;
            --text-primary: #172033;
            --text-secondary: #64748b;
            --border: #e5eaf2;
            --danger: #ef4444;
            --sidebar-width: 258px;
            --navbar-height: 74px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI",
                         Arial, sans-serif;
            background: var(--page-bg);
            color: var(--text-primary);
            font-size: 14px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: var(--blue-600);
            text-decoration: none;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        button,
        a {
            -webkit-tap-highlight-color: transparent;
        }

        /* Muted premium navbar */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            min-height: var(--navbar-height);
            padding: 0 30px;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            background: #f1f3f6;
            border-bottom: 1px solid #dfe4eb;
            box-shadow: none;
        }

        .navbar-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            color: #202b3b;
            text-decoration: none;
        }

        .navbar-brand:hover {
            color: #35465e;
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            color: #e2e8f0;
            background: #24344b;
            border: 1px solid #34465f;
            border-radius: 10px;
            box-shadow: none;
            font-size: 19px;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            gap: 1px;
            min-width: 0;
        }

        .brand-title {
            color: #202b3b;
            font-size: 15px;
            font-weight: 750;
            letter-spacing: -0.25px;
        }

        .brand-subtitle {
            color: #7a8698;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        /* Notification dropdown */
        .navbar-notifications {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            flex-shrink: 0;
            visibility: visible;
            opacity: 1;
        }

        .navbar-notifications details {
            position: relative;
        }

        .navbar-notification-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            width: 42px;
            height: 42px;
            color: #35465e;
            background: #e5e9ef;
            border: 1px solid #d5dce6;
            border-radius: 50%;
            cursor: pointer;
            list-style: none;
        }

        .navbar-notification-toggle::-webkit-details-marker {
            display: none;
        }

        .navbar-notification-toggle i {
            display: block;
            font-size: 17px;
        }

        .navbar-notification-toggle:hover {
            background: #dce2ea;
        }

        .navbar-notification-toggle:focus-visible {
            outline: 2px solid #64748b;
            outline-offset: 3px;
        }

        .notification-count {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: #dc3545;
            border: 2px solid #f1f3f6;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
        }

        .notification-dropdown {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            z-index: 9999;
            width: 340px;
            max-width: calc(100vw - 28px);
            background: #fff;
            border: 1px solid #dfe4eb;
            border-radius: 12px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.14);
        }

        .notification-dropdown-header {
            padding: 15px 16px;
            border-bottom: 1px solid #e8edf3;
            color: #202b3b;
            font-size: 14px;
            font-weight: 700;
        }

        .notification-item {
            padding: 13px 16px;
            border-bottom: 1px solid #edf0f4;
        }

        .notification-item.unread {
            background: #f0f6ff;
        }

        .notification-item p {
            margin: 0 0 6px;
            color: #35465e;
            font-size: 13px;
            line-height: 1.5;
        }

        .notification-item small {
            display: block !important;
            margin-top: 5px;
            color: #64748b !important;
            font-size: 12px;
            font-weight: 500;
            line-height: 1.5;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .notification-item small,
        .notification-item small * {
            visibility: visible;
            opacity: 1;
        }

        .notification-read-form {
            margin-top: 8px;
        }

        .notification-read-button {
            padding: 0;
            color: #2563eb;
            background: none;
            border: 0;
            font-size: 12px;
            font-weight: 650;
            cursor: pointer;
        }

        .notification-empty {
            padding: 22px 16px;
            color: #7a8698;
            text-align: center;
            font-size: 13px;
        }

        @media (max-width: 640px) {
            .navbar-notification-toggle {
                width: 36px;
                height: 36px;
            }

            .notification-dropdown {
                right: -84px;
            }
        }

        .navbar-profile-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 42px;
            height: 42px;
            padding: 0;
            background: #e5e9ef;
            border: 1px solid #d5dce6;
            border-radius: 50%;
            text-decoration: none;
            overflow: hidden;
            transition: background 0.2s ease, border-color 0.2s ease;
        }

        .navbar-profile-link:hover {
            background: #dce2ea;
            border-color: #c4cedb;
        }

        .navbar-profile-link:focus-visible {
            outline: 2px solid #64748b;
            outline-offset: 3px;
        }

        .navbar-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            color: #35465e;
            background: #e5e9ef;
            border-radius: 50%;
            font-size: 15px;
            font-weight: 750;
            overflow: hidden;
        }

        .navbar-avatar-image {
            display: block;
            object-fit: cover;
            border: 0;
        }

        .navbar-profile-arrow {
            color: #7a8698;
            font-size: 11px;
        }

        .navbar-login {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            color: #35465e;
            background: #e5e9ef;
            border: 1px solid #d5dce6;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 650;
        }

        .navbar-login:hover {
            color: #202b3b;
            background: #dce2ea;
        }

        /* Application layout */
        .app-container {
            display: flex;
            align-items: stretch;
            min-height: calc(100vh - var(--navbar-height));
        }

        /* Sidebar */
        .sidebar {
            display: flex;
            flex: 0 0 var(--sidebar-width);
            flex-direction: column;
            width: var(--sidebar-width);
            min-width: 0;
            min-height: calc(100vh - var(--navbar-height));
            padding: 24px 16px;
            border-radius: 0;
            color: #cbd5e1;
            background: linear-gradient(180deg, var(--navy-900), var(--navy-950));
            border-right: 1px solid rgba(255, 255, 255, 0.04);
        }

        .sidebar-heading {
            margin: 0 0 12px;
            padding: 0 12px;
            color: #8290a8;
            font-size: 10px;
            font-weight: 750;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .sidebar-nav {
            display: flex;
            flex: 1;
            flex-direction: column;
            gap: 6px;
            min-height: 0;
        }

        .sidebar-nav > a {
            display: flex;
            align-items: center;
            gap: 13px;
            min-height: 46px;
            padding: 11px 13px;
            color: #c0cada;
            border: 1px solid transparent;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 550;
            transition: background 0.18s ease, color 0.18s ease,
                        border-color 0.18s ease, transform 0.18s ease;
        }

        .sidebar-nav > a i {
            width: 20px;
            color: #91a0b8;
            font-size: 17px;
            text-align: center;
        }

        .sidebar-nav > a:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.07);
        }

        .sidebar-nav > a:hover i {
            color: #fff;
        }

        .sidebar-nav > a.active {
            color: #fff;
            background: linear-gradient(100deg, #2563eb, #3478f6);
            border-color: rgba(147, 197, 253, 0.18);
            box-shadow: 0 5px 14px rgba(37, 99, 235, 0.2);
            font-weight: 650;
        }

        .sidebar-nav > a.active i {
            color: #fff;
        }

        .sidebar-divider {
            height: 1px;
            margin: 18px 4px;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-logout-form {
            margin-top: auto;
            padding-top: 20px;
        }

        .sidebar-logout {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 12px;
            width: 100%;
            min-height: 46px;
            padding: 11px 13px;
            color: #fecaca;
            background: rgba(239, 68, 68, 0.09);
            border: 1px solid rgba(248, 113, 113, 0.2);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 650;
            text-align: left;
            cursor: pointer;
            transition: background 0.18s ease, color 0.18s ease;
        }

        .sidebar-logout i {
            width: 20px;
            font-size: 17px;
            text-align: center;
        }

        .sidebar-logout:hover {
            color: #fff;
            background: #dc2626;
        }

        /* Main content */
        .main-content {
            flex: 1;
            min-width: 0;
            padding: 32px;
        }

        .main-content h1 {
            margin: 0 0 8px;
            color: var(--text-primary);
            font-size: clamp(23px, 2.5vw, 30px);
            font-weight: 750;
            line-height: 1.3;
            letter-spacing: -0.8px;
        }

        .main-content h2,
        .main-content h3 {
            color: var(--text-primary);
            letter-spacing: -0.2px;
        }

        .main-content p {
            line-height: 1.7;
        }

        /* Shared cards */
        .dashboard-card,
        .profile-card,
        .content-card {
            margin-bottom: 24px;
            padding: 24px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 3px 12px rgba(15, 23, 42, 0.035);
        }

        .dashboard-card {
            border-top: 3px solid var(--blue-600);
        }

        /* Form controls */
        .main-content input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
        .main-content select,
        .main-content textarea {
            max-width: 100%;
            border-radius: 8px;
        }

        .main-content input:focus,
        .main-content select:focus,
        .main-content textarea:focus {
            outline: 2px solid rgba(59, 130, 246, 0.2);
            outline-offset: 1px;
        }

        .main-content button,
        .main-content input[type="submit"] {
            font: inherit;
        }

        /* Tables */
        .main-content table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        .main-content th,
        .main-content td {
            padding: 13px 15px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            vertical-align: middle;
        }

        .main-content th {
            color: #536178;
            background: #f8fafc;
            font-size: 12px;
            font-weight: 750;
            letter-spacing: 0.25px;
        }

        .main-content tbody tr:hover {
            background: #fafcff;
        }

        /* Alerts */
        .alert,
        .success,
        .error {
            padding: 13px 16px;
            margin-bottom: 18px;
            border-radius: 10px;
        }

        .alert-success,
        .success {
            color: #166534;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .alert-error,
        .error {
            color: #991b1b;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        /* Footer */
        .app-footer {
            padding: 18px 24px;
            color: #8490a3;
            background: var(--page-bg);
            border-top: 1px solid var(--border);
            border-radius: 0;
            font-size: 12px;
            text-align: center;
        }

        .app-footer p {
            margin: 0;
        }

        /* Laravel pagination */
        .main-content .pagination svg {
            display: inline-block;
            width: 16px !important;
            height: 16px !important;
            max-width: 16px;
            vertical-align: middle;
        }

        .main-content .pagination a,
        .main-content .pagination span {
            line-height: 1.5;
        }

        /* Tablet and mobile */
        @media (max-width: 900px) {
            :root {
                --sidebar-width: 220px;
            }

            .navbar {
                padding: 0 22px;
            }

            .main-content {
                padding: 24px 20px;
            }

            .sidebar {
                padding: 20px 12px;
            }
        }

        @media (max-width: 640px) {
            .navbar {
                min-height: 66px;
                padding: 10px 14px;
                gap: 10px;
            }

            .brand-mark {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                font-size: 17px;
            }

            .navbar-brand {
                gap: 9px;
            }

            .brand-title {
                font-size: 12px;
            }

            .brand-subtitle {
                font-size: 10px;
            }

            .navbar-profile-link {
                width: 36px;
                height: 36px;
            }

            .navbar-profile-arrow {
                display: none;
            }

            .app-container {
                flex-direction: column;
                min-height: auto;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
                padding: 12px;
                border-right: 0;
                border-bottom: 1px solid var(--border);
            }

            .sidebar-heading {
                margin-bottom: 8px;
            }

            .sidebar-nav {
                flex: none;
                flex-direction: row;
                flex-wrap: wrap;
                gap: 6px;
            }

            .sidebar-nav > a {
                flex: 1 1 calc(50% - 6px);
                min-width: 0;
                min-height: 42px;
                padding: 9px 10px;
                gap: 8px;
                font-size: 12px;
            }

            .sidebar-nav > a i {
                font-size: 15px;
            }

            .sidebar-divider {
                display: none;
            }

            .sidebar-logout-form {
                flex: 1 1 calc(50% - 6px);
                min-width: 0;
                margin-top: 0;
                padding-top: 0;
            }

            .sidebar-logout {
                min-height: 42px;
                padding: 9px 10px;
                gap: 8px;
                font-size: 12px;
            }

            .main-content {
                padding: 22px 15px;
            }

            .main-content h1 {
                font-size: 24px;
            }

            .dashboard-card,
            .profile-card,
            .content-card {
                padding: 18px;
                border-radius: 12px;
            }

            .main-content table {
                min-width: 650px;
            }

            .app-footer {
                padding: 16px;
            }
        }

        @stack('styles')

        .notification-dropdown .notification-item .notification-timestamp {
            display: block !important;
            margin-top: 8px !important;
            margin-bottom: 8px !important;
            color: #475569 !important;
            background: transparent !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .notification-dropdown .notification-item .notification-timestamp span {
            display: inline-block !important;
            color: #475569 !important;
            background: transparent !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            line-height: 20px !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="app-container">
        @auth
            @include('partials.sidebar')
        @endauth

        <main class="main-content">
            @include('partials.alerts')
            @yield('content')
        </main>
    </div>

    @include('partials.footer')

    @stack('scripts')
</body>
</html>