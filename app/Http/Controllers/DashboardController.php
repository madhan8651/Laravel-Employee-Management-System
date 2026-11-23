<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->role === Role::Employee) {
            return view('dashboard.employee', [
                'user' => $user,
                'employee' => $user->employee,
            ]);
        }

        $statistics = Cache::remember(
            'dashboard.statistics',
            now()->addMinutes(10),
            function () {
                // Employee statistics exclude soft-deleted employees.
                $totalEmployees = Employee::count();

                $activeEmployees = Employee::where('status', 'active')
                    ->count();

                $inactiveEmployees = Employee::where('status', 'inactive')
                    ->count();

                $totalDepartments = Department::count();

                $employeesByDepartment = Department::query()
                    ->withCount('employees')
                    ->orderBy('name')
                    ->get();

                $departmentNames = [
                    'IT',
                    'HR',
                    'Finance',
                    'Sales',
                    'Operations',
                ];

                $departmentStats = collect($departmentNames)->map(
                    function ($name) use ($employeesByDepartment) {
                        $department = $employeesByDepartment->first(
                            function ($department) use ($name) {
                                $departmentName = (string) $department->getAttribute('name');

                                return strcasecmp($departmentName, $name) === 0
                                    || $name === 'IT'
                                        && strcasecmp(
                                            $departmentName,
                                            'Information Technology'
                                        ) === 0
                                    || $name === 'HR'
                                        && strcasecmp(
                                            $departmentName,
                                            'Human Resources'
                                        ) === 0;
                            }
                        );

                        return [
                            'name' => $name,
                            'employees_count' => (int) (
                                $department?->getAttribute('employees_count') ?? 0
                            ),
                        ];
                    }
                )->all();

                return compact(
                    'totalEmployees',
                    'activeEmployees',
                    'inactiveEmployees',
                    'totalDepartments',
                    'departmentStats'
                );
            }
        );

        return view('dashboard.index', [
            'user' => $user,
            ...$statistics,
        ]);
    }
}
