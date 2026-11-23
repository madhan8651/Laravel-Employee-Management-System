# Laravel Employee Management System

A role-based Employee Management System built with Laravel 13, PHP, and
MySQL/MariaDB. The application brings employee and department
administration, authentication, profile management, REST APIs,
reporting, notifications, background jobs, and automated testing into
one platform.

> **Project status:** Development project. The commands, test results,
> and configuration details below reflect the project state verified
> during the Module 14 work. Adapt environment-specific values before
> using them on another machine.

## Table of Contents

1.  [Project Overview](#project-overview)
2.  [Features](#features)
3.  [Technologies Used](#technologies-used)
4.  [Requirements](#requirements)
5.  [Installation](#installation)
6.  [Database Setup](#database-setup)
7.  [Environment and Configuration](#environment-and-configuration)
8.  [Project Structure](#project-structure)
9.  [Laravel Fundamentals](#laravel-fundamentals)
10. [Routing and Controllers](#routing-and-controllers)
11. [User Roles and Permissions](#user-roles-and-permissions)
12. [API Documentation](#api-documentation)
13. [Testing and Code Quality](#testing-and-code-quality)
14. [Performance and Optimization](#performance-and-optimization)
15. [Project Documentation](#project-documentation)
16. [Known Issues and Notes](#known-issues-and-notes)
17. [Future Enhancements](#future-enhancements)
18. [License](#license)

------------------------------------------------------------------------

## Project Overview

The Laravel Employee Management System (EMS) is designed to simplify
employee administration and provide a centralized platform for managing
employee information.

The application follows Laravel's MVC architecture and uses Eloquent
ORM, Form Requests, middleware, policies, service classes, repositories,
events, listeners, queues, and notifications.

The application supports two primary user roles: Administrator and
Employee.

## Features

### Authentication and Authorization

-   User login and logout.
-   Password reset and password setup workflows.
-   Role-based access control.
-   Admin and employee access restrictions.
-   Account status verification.
-   Laravel Sanctum API authentication.

### Employee Management

-   Create, view, update, and delete employees.
-   Employee profile management.
-   Employee code validation.
-   Department assignment.
-   Employee status management.
-   Profile photo upload and private photo access.
-   Search, filtering, sorting, and pagination.
-   Soft deletion support.

### Department Management

-   Create and manage departments.
-   Update department information.
-   Department employee counts.
-   Salary statistics by department.
-   Department-level validation and authorization.

### Additional Features

-   Dashboard statistics with caching.
-   Employee reports and CSV export.
-   Activity logging.
-   Email notifications.
-   Queued background jobs.
-   Employee-created events and listeners.
-   Birthday reminder scheduling.
-   API endpoints for employee and department data.
-   Security headers and server-side validation.

## Technologies Used

  Technology          Purpose
  ------------------- --------------------------------------
  PHP 8.3+            Backend programming
  Laravel 13          Application framework
  MySQL / MariaDB     Database
  Eloquent ORM        Database operations
  Laravel Sanctum     API authentication
  Blade               Server-side views
  PHPUnit             Automated testing
  Laravel Pint        Code formatting
  Laravel Telescope   Debugging and performance monitoring
  Composer            Dependency management
  Git and GitHub      Version control

## Requirements

-   PHP 8.3 or later.
-   Composer.
-   MySQL or MariaDB.
-   Node.js and npm, if compiling frontend assets.
-   Git.
-   A compatible web server or Laravel development server.

Required PHP extensions should satisfy Laravel's framework requirements.

## Installation

### 1. Clone the repository

``` bash
git clone <YOUR_REPOSITORY_URL>
cd employee-management
```

Replace `<YOUR_REPOSITORY_URL>` with the repository's clone URL.

### 2. Install PHP dependencies

``` bash
composer install
```

### 3. Install frontend dependencies

``` bash
npm install
```

### 4. Create the environment file

Windows PowerShell:

``` powershell
Copy-Item .env.example .env
```

Linux/macOS:

``` bash
cp .env.example .env
```

### 5. Generate the application key

``` bash
php artisan key:generate
```

### 6. Configure the database

Update the database connection details in `.env`.

### 7. Run migrations

``` bash
php artisan migrate
```

If initial development data is needed, review the seeders and then run:

``` bash
php artisan db:seed
```

### 8. Create the storage link

``` bash
php artisan storage:link
```

### 9. Build frontend assets

``` bash
npm run build
```

### 10. Start the development server

``` bash
php artisan serve
```

Open `http://127.0.0.1:8000` in your browser.

## Database Setup

The application uses MySQL/MariaDB with Laravel's Eloquent ORM.

Create a database before running migrations. For example:

``` sql
CREATE DATABASE laravel_employee_management;
```

Configure the connection in `.env`:

``` env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_employee_management
DB_USERNAME=root
DB_PASSWORD=
```

Adjust the port, database name, username, and password to match your
local environment. The local development database may use a different
port.

Useful database commands:

``` bash
php artisan migrate:status
php artisan migrate
php artisan db:seed
```

**Important:** Never commit database credentials or production data to
GitHub.

## Environment and Configuration

Important environment variables include:

  Variable             Purpose
  -------------------- ----------------------------
  `APP_NAME`           Application name
  `APP_ENV`            Application environment
  `APP_KEY`            Application encryption key
  `APP_DEBUG`          Debug mode
  `APP_URL`            Application URL
  `DB_CONNECTION`      Database driver
  `DB_HOST`            Database host
  `DB_PORT`            Database port
  `DB_DATABASE`        Database name
  `DB_USERNAME`        Database username
  `DB_PASSWORD`        Database password
  `MAIL_MAILER`        Mail transport
  `MAIL_HOST`          SMTP host
  `MAIL_PORT`          SMTP port
  `CACHE_STORE`        Cache driver
  `QUEUE_CONNECTION`   Queue driver

Use `env()` inside configuration files and `config()` in application
code. Avoid calling `env()` directly from application code because
configuration caching can change how environment values are loaded.

Example:

``` php
// In a configuration file
'name' => env('APP_NAME', 'Laravel'),

// In application code
$appName = config('app.name');
$timezone = config('app.timezone');
```

The `.env` file contains environment-specific settings and must not be
committed. The `.env.example` file should contain safe example values
without secrets.

------------------------------------------------------------------------

## Laravel Fundamentals

This section retains the foundational Laravel learning and
implementation notes from the original README.

### 1. Laravel Folder Structure

  -----------------------------------------------------------------------
  Folder/File                         Purpose
  ----------------------------------- -----------------------------------
  `app/`                              Application code, including models,
                                      controllers, middleware, services,
                                      and providers.

  `bootstrap/`                        Initializes the Laravel application
                                      and contains bootstrap
                                      configuration.

  `config/`                           Stores application and service
                                      configuration files.

  `database/`                         Contains migrations, factories, and
                                      seeders.

  `public/`                           Contains `index.php`, the HTTP
                                      entry point, and public assets.

  `resources/`                        Contains Blade views and frontend
                                      source files.

  `routes/`                           Contains route definitions,
                                      including `web.php`, `api.php`, and
                                      console routes.

  `storage/`                          Stores logs, caches, compiled
                                      views, and generated files.

  `tests/`                            Contains automated unit and feature
                                      tests.

  `vendor/`                           Contains Composer-installed
                                      dependencies, including Laravel.
  -----------------------------------------------------------------------

### 2. Laravel Request Lifecycle

The request lifecycle describes how Laravel receives an HTTP request,
processes it, and returns a response.

``` text
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

1.  `public/index.php` is the entry point for incoming HTTP requests.
2.  `bootstrap/app.php` creates and configures the Laravel application.
3.  Laravel bootstraps the application and loads registered service
    providers.
4.  The router matches the URL and HTTP method to a route.
5.  Middleware performs request-processing checks, such as
    authentication and session handling.
6.  The controller or route handler executes application logic and may
    interact with models or the database.
7.  Laravel creates the HTTP response and sends it back to the browser.

### 3. Service Provider Registration

**Registration file:** `bootstrap/providers.php`

The application registers `AppServiceProvider`:

``` php
<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
];
```

**Provider file:** `app/Providers/AppServiceProvider.php`

-   `register()` registers services and bindings in Laravel's service
    container.
-   `boot()` initializes application services after providers have been
    registered.

In this project, the provider is used for application service bindings,
API rate-limiter configuration, model observer registration, and shared
data for selected views. It is not an empty default provider.

### 4. Useful Artisan Commands

  ----------------------------------------------------------------------------------------
  Command                                              Purpose
  ---------------------------------------------------- -----------------------------------
  `php artisan --version`                              Displays the Laravel version.

  `php artisan about`                                  Displays application information.

  `php artisan serve`                                  Starts the development server.

  `php artisan route:list`                             Lists registered routes.

  `php artisan make:controller ExampleController`      Creates a controller.

  `php artisan make:model Example`                     Creates a model.

  `php artisan make:migration create_examples_table`   Creates a migration.

  `php artisan migrate:status`                         Displays migration status.

  `php artisan config:clear`                           Clears the configuration cache.
  ----------------------------------------------------------------------------------------

### 5. Project Verification

The project environment was previously verified with the following
details:

-   Laravel Framework: 13.43.0 (current version recorded during Module
    14 work).
-   PHP CLI: 8.5.9 (local development environment at the time of
    verification).
-   Database driver: MySQL-compatible connection to MariaDB.
-   Registered application provider: `AppServiceProvider`.

PHP versions and database settings can differ between the command-line
environment and the web server. Check the current environment with:

``` bash
php artisan about
php artisan --version
php -v
composer --version
```

The `.env` file controls environment-specific database settings. Do not
publish database credentials.

## Routing and Controllers

The application uses Laravel resource routes, named routes, route
groups, authentication middleware, numeric parameter constraints, and
employee-code route model binding.

### Route Documentation

The complete registered route list is available here:

-   [View the route list](docs/route-list.txt)

To regenerate the route documentation after changing routes:

``` bash
php artisan route:list > docs/route-list.txt
```

### Implemented Routing and Controller Features

-   Resource routes for employees, departments, and users.
-   Dashboard controller.
-   Named and grouped routes.
-   Authentication middleware for admin and employee sections.
-   Numeric route parameter constraints.
-   Employee-code route model binding.
-   JSON employee reports and CSV export.
-   Custom 404 view.
-   Login and logout controller methods.

------------------------------------------------------------------------

## Project Structure

Important application directories:

  Directory                 Purpose
  ------------------------- ------------------------------------
  `app/Console/Commands/`   Custom Artisan commands
  `app/Contracts/`          Application interfaces
  `app/Enums/`              Application enums
  `app/Events/`             Application events
  `app/Exceptions/`         Custom exceptions
  `app/Http/Controllers/`   Web and API controllers
  `app/Http/Middleware/`    Request middleware
  `app/Http/Requests/`      Form validation
  `app/Http/Resources/`     API resources
  `app/Jobs/`               Background jobs
  `app/Listeners/`          Event listeners
  `app/Models/`             Eloquent models
  `app/Notifications/`      Notifications
  `app/Observers/`          Model observers
  `app/Policies/`           Authorization policies
  `app/Repositories/`       Data access implementations
  `app/Services/`           Business logic
  `app/Traits/`             Reusable functionality
  `bootstrap/`              Application bootstrap
  `config/`                 Configuration files
  `database/`               Migrations, factories, and seeders
  `docs/`                   Project documentation
  `lang/`                   Translation files
  `resources/views/`        Blade templates
  `routes/`                 Web, API, and console routes
  `storage/`                Logs and generated files
  `tests/Feature/`          Feature tests
  `tests/Unit/`             Unit tests

## User Roles and Permissions

### Administrator

-   Manage employees.
-   Manage departments.
-   Manage user accounts.
-   Access administrative dashboards and reports.
-   View authorized employee activity.

### Employee

-   Access their own profile.
-   Update permitted profile information.
-   Manage permitted account information.
-   Access authorized employee features.

Authorization is enforced through middleware, policies, gates, and
server-side validation.

## API Documentation

The application provides REST API endpoints for authentication,
employees, and departments. API authentication uses Laravel Sanctum.

-   [API Documentation](docs/API_DOCUMENTATION.md)
-   [Registered Route List](docs/route-list.txt)

Use authenticated API requests with the appropriate Sanctum bearer token
where required.

## Testing and Code Quality

The project uses PHPUnit for automated testing.

Run the complete test suite:

``` bash
php artisan test
```

Run feature tests:

``` bash
php artisan test --testsuite=Feature
```

Run unit tests:

``` bash
php artisan test --testsuite=Unit
```

Run Laravel Pint:

``` bash
vendor/bin/pint
```

Verify code formatting:

``` bash
vendor/bin/pint --test
```

**Latest verified results during the Module 14 work:**

-   111 tests passed.
-   396 assertions.
-   No test failures reported.
-   Pint verification passed for 125 PHP files.

Test areas include authentication, authorization, employee CRUD,
department CRUD, validation, file uploads, API authentication, search,
pagination, and security.

## Performance and Optimization

The project includes:

-   Dashboard statistics caching using `Cache::remember()`.
-   Cache invalidation when employee or department data changes.
-   Eloquent eager loading to reduce unnecessary database queries.
-   Database indexes for selected fields.
-   Query monitoring through Laravel Telescope.
-   Pagination for employee and department listings.

The configured cache store during the Module 14 review was the database
cache driver. Laravel Telescope was used to inspect requests and
database queries during development.

## Project Documentation

Additional documentation is available in the `docs/` directory:

  ---------------------------------------------------------------------------------
  File                                          Description
  --------------------------------------------- -----------------------------------
  `API_DOCUMENTATION.md`                        REST API reference

  `route-list.txt`                              Registered application routes

  `Laravel EMS Test Cases.xlsx`                 Test case documentation

  `Security Implementation and Auditent.docx`   Security implementation and audit
                                                documentation
  ---------------------------------------------------------------------------------

## Known Issues and Notes

-   Database ports and credentials are environment-specific. Configure
    `.env` for the machine where the project is running.
-   Telescope is intended for development diagnostics; review its access
    and configuration before using it in any shared environment.
-   Performance observations depend on the local database, dataset size,
    cache state, and runtime environment.

No additional unresolved critical application issue is asserted here.

## Future Enhancements

Potential improvements include:

-   Advanced employee analytics and reporting.
-   Expanded automated test coverage.
-   Additional API endpoints.
-   Improved dashboard visualizations.
-   More detailed activity and audit reports.
-   Enhanced documentation and architecture diagrams.

## License

This project uses the Laravel framework, which is open-source software
licensed under the [MIT License](https://opensource.org/licenses/MIT).
Add or update the application's own license file if a separate project
license is intended.

------------------------------------------------------------------------

**Developed as a Laravel Employee Management System project.**
