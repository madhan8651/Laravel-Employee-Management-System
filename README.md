<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Module 1: Laravel Setup and Fundamentals

### 1. Laravel Folder Structure

| Folder/File | Purpose |
|---|---|
| `app/` | Contains application code, including models, controllers, middleware, and providers. |
| `bootstrap/` | Initializes the Laravel application and contains bootstrap configuration. |
| `config/` | Stores application and service configuration files. |
| `database/` | Contains migrations, factories, and seeders. |
| `public/` | Contains `index.php`, the HTTP entry point, and public assets. |
| `resources/` | Contains Blade views and frontend source files. |
| `routes/` | Contains route definitions, including `web.php` and `console.php`. |
| `storage/` | Stores logs, caches, compiled views, and generated files. |
| `tests/` | Contains automated unit and feature tests. |
| `vendor/` | Contains Composer-installed dependencies, including Laravel. |

### 2. Laravel Request Lifecycle

The request lifecycle describes how Laravel receives an HTTP request, processes it, and returns a response.

```text
Browser Request
      |
      v
public/index.php
      |
      v
bootstrap/app.php
      |
      v
Application Bootstrap and Service Providers
      |
      v
Router
      |
      v
Middleware
      |
      v
Controller / Route Handler
      |
      v
HTTP Response
      |
      v
Browser
```

**Lifecycle steps:**

1. `public/index.php` is the entry point for incoming HTTP requests.
2. `bootstrap/app.php` creates and configures the Laravel application.
3. Laravel bootstraps the application and loads registered service providers.
4. The router matches the URL and HTTP method to a route.
5. Middleware performs request-processing checks, such as authentication and session handling.
6. The controller or route handler executes application logic and may interact with models or the database.
7. Laravel creates the HTTP response and sends it back to the browser.

### 3. Service Provider Registration

**Registration file:** `bootstrap/providers.php`

This project registers `AppServiceProvider`:

```php
<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
];
```

**Provider file:** `app/Providers/AppServiceProvider.php`

- `register()` registers services and bindings in Laravel's service container.
- `boot()` initializes application services after providers have been registered.

Both methods are currently empty in this project, which is normal for a new application.

### 4. Useful Artisan Commands

| Command | Purpose |
|---|---|
| `php artisan --version` | Displays the Laravel version. |
| `php artisan about` | Displays application information. |
| `php artisan serve` | Starts the development server. |
| `php artisan route:list` | Lists registered routes. |
| `php artisan make:controller ExampleController` | Creates a controller. |
| `php artisan make:model Example` | Creates a model. |
| `php artisan make:migration create_examples_table` | Creates a migration. |
| `php artisan migrate:status` | Displays migration status. |
| `php artisan config:clear` | Clears the configuration cache. |

### 5. Project Verification

Verified using Artisan commands:

- Laravel Framework: 13.37.0
- PHP: 8.5.9
- Composer: 2.10.3
- Environment: local
- Registered routes: 4
- Registered application provider: `AppServiceProvider`
- Database driver reported by Laravel: MySQL

The application uses MySQL, configured through the .env file. Database credentials must not be committed.



### 6. Environment and Configuration

- Use env() inside configuration files in config/ to read environment variables.
- Use config() in application code to read Laravel configuration values.
- Avoid calling env() directly in application code because configuration caching can cause unexpected results.

Examples:

```php
// In a configuration file
'name' => env('APP_NAME', 'Laravel'),

// In application code
$appName = config('app.name');
$timezone = config('app.timezone');
```

The .env file contains environment-specific settings and must not be committed. The .env.example file should contain safe example values without secrets.

## Module 2: Routing and Controllers

The application uses Laravel resource routes, named routes, route groups, authentication middleware, numeric parameter constraints, and employee-code route model binding.

### Route Documentation

The complete registered route list is available here:

- [View the route list](docs/route-list.txt)

To regenerate the route documentation after changing routes, run:

```bash
php artisan route:list > docs/route-list.txt
```

### Implemented Features

- Resource routes for employees, departments, and users
- Single-action dashboard controller
- Named and grouped routes
- Authentication middleware for admin and employee sections
- Numeric route parameter constraints
- Explicit employee-code model binding
- JSON employee reports and CSV export
- Custom 404 view
- Login and logout controller methods
