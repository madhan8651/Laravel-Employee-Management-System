<?php

namespace App\Contracts;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeRepositoryInterface
{
    public function getAll(): Collection;

    public function paginate(?int $perPage = null): LengthAwarePaginator;

    public function findById(int $id): Employee;

    public function paginateFiltered(
        array $filters,
        string $sortBy,
        string $direction,
        ?int $perPage = null
    ): LengthAwarePaginator;
}
