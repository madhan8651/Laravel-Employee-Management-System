<?php

namespace App\Http\Middleware;

use App\Enums\EmployeeStatus;
use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        // Administrators do not require an employee record.
        if ($user->role === Role::Admin) {
            return $next($request);
        }

        $employee = $user->employee;

        if (! $employee || $employee->status !== EmployeeStatus::Active) {
            abort(403, 'Your employee account is inactive or unavailable.');
        }

        return $next($request);
    }
}
