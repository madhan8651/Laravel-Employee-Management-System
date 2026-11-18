<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\PostTooLargeException;
use App\Exceptions\DepartmentHasEmployeesException;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\TrackRequestContext;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {


        // Enable Sanctum authentication for first-party SPA requests.
        $middleware->statefulApi();

        // Global middleware
        $middleware->throttleApi();

        $middleware->append(TrackRequestContext::class);
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Route middleware aliases
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'active' => EnsureUserIsActive::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Preserve JSON responses for API requests.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) =>
                $request->is('api/*') || $request->expectsJson(),
        );

        // Handle attempts to delete departments that still have employees.
        $exceptions->render(function (
            DepartmentHasEmployeesException $exception,
            Request $request
        ) {
            if ($request->is('admin/departments*')) {
                return redirect()
                    ->route('admin.departments.index')
                    ->with('error', $exception->getMessage());
            }

            return null;
        });
        // Handle requests rejected because they exceed PHP's POST limit.
        $exceptions->render(function (
            PostTooLargeException $exception,
            Request $request
        ) {
            if ($request->is('admin/employees/*')) {
                return redirect()
                    ->route('admin.employees.index')
                    ->with(
                        'error',
                        'The uploaded request is too large. Please select a profile photo smaller than 2 MB.'
                    );
            }

            // Allow Laravel to handle other oversized requests normally.
            return null;
        });

    })
    ->create();


