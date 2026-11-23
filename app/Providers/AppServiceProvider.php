<?php

namespace App\Providers;

use App\Contracts\EmployeeRepositoryInterface;
use App\Enums\Role;
use App\Models\Employee;
use App\Observers\EmployeeObserver;
use App\Repositories\EloquentEmployeeRepository;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            EmployeeRepositoryInterface::class,
            EloquentEmployeeRepository::class
        );

        if ($this->app->environment('local')) {
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    public function boot(): void
    {
        // API rate limiter: 60 requests per minute per user or IP.
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by(
                $request->user()?->id ?: $request->ip()
            );
        });

        // Register the Employee observer.
        Employee::observe(EmployeeObserver::class);

        // Authorization gate for employee management.
        Gate::define('manage-employees', function ($user) {
            return $user->role === Role::Admin;
        });

        // Authorization gate for user management.
        Gate::define('manage-users', function ($user) {
            return $user->role === Role::Admin;
        });

        // Available to all Blade views.
        View::share('appName', config('app.name'));

        // Available only to admin views.
        View::composer('admin.*', function ($view) {
            $view->with('totalEmployees', Employee::count());
        });
    }
}
