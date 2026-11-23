<?php

namespace App\Repositories;

use App\Contracts\EmployeeRepositoryInterface;
use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentEmployeeRepository implements EmployeeRepositoryInterface
{
    public function getAll(): Collection
    {
        return Employee::all();
    }

    public function paginate(?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= (int) config('ems.pagination.employees', 10);

        return Employee::latest()->paginate($perPage);
    }

    public function findById(int $id): Employee
    {
        return Employee::findOrFail($id);
    }

    public function paginateFiltered(
        array $filters,
        string $sortBy,
        string $direction,
        ?int $perPage = null
    ): LengthAwarePaginator {
        $perPage ??= (int) config('ems.pagination.employees', 10);
        $query = Employee::with('department');

        // Search by name, email, or employee code.
        $query->when(
            ($filters['search'] ?? '') !== '',
            function ($query) use ($filters) {
                $search = $filters['search'];

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%");
                });
            }
        );

        // Filter by department.
        $query->when(
            ($filters['department_id'] ?? '') !== '',
            fn ($query) => $query->where(
                'department_id',
                $filters['department_id']
            )
        );

        // Filter by employment status.
        $query->when(
            ($filters['status'] ?? '') !== '',
            fn ($query) => $query->where(
                'status',
                $filters['status']
            )
        );

        // Filter by gender.
        $query->when(
            ($filters['gender'] ?? '') !== '',
            fn ($query) => $query->where(
                'gender',
                $filters['gender']
            )
        );

        return $query
            ->orderBy($sortBy, $direction)
            ->paginate($perPage);
    }
}
