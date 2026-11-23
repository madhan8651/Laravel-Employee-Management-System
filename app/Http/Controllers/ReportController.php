<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-employees');
        $filters = $request->only(['search', 'department_id']);

        $employees = Employee::query()
            ->with('department')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('department_id'), function ($query) use ($request) {
                $query->where('department_id', $request->input('department_id'));
            })
            ->orderBy('name')
            ->get();

        // Group the filtered employees by designation.
        $employeesByDesignation = $employees->groupBy(
            fn ($employee) => $employee->designation ?: 'Unassigned'
        );

        // Calculate the total salary of the filtered employees.
        $totalSalary = $employees->sum('salary');

        $departments = Department::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.reports.index', compact(
            'filters',
            'employees',
            'employeesByDesignation',
            'totalSalary',
            'departments'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        Gate::authorize('manage-employees');

        return response()->streamDownload(function () use ($request) {
            $output = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility.
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'Employee Code',
                'Name',
                'Email',
                'Designation',
                'Phone',
            ]);

            $employees = Employee::query()
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = $request->input('search');

                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('employee_code', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->when($request->filled('department_id'), function ($query) use ($request) {
                    $query->where('department_id', $request->input('department_id'));
                })
                ->orderBy('id');

            $employees->chunk(100, function ($employees) use ($output) {
                foreach ($employees as $employee) {
                    fputcsv($output, [
                        $employee->employee_code,
                        $employee->name,
                        $employee->email,
                        $employee->designation,
                        "\t".(string) ($employee->phone ?? ''),
                    ]);
                }
            });

            fclose($output);
        }, 'employees.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
