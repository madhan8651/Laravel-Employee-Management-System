<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiSearchPaginationTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateAdmin(): User
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        Sanctum::actingAs($admin);

        return $admin;
    }

    public function test_employee_api_returns_paginated_json_structure(): void
    {
        $this->authenticateAdmin();

        Employee::factory()->count(3)->create();

        $response = $this->getJson('/api/employees?per_page=2');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'employee_code',
                        'name',
                        'email',
                        'department',
                        'permissions' => [
                            'view',
                            'update',
                            'delete',
                        ],
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
                'status',
                'message',
            ])
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 3);
    }

    public function test_employee_api_can_search_by_employee_code(): void
    {
        $this->authenticateAdmin();

        Employee::factory()->create([
            'employee_code' => 'API-SEARCH-001',
            'name' => 'API Search Employee',
        ]);

        Employee::factory()->create([
            'employee_code' => 'API-OTHER-002',
            'name' => 'Other Employee',
        ]);

        $response = $this->getJson('/api/employees?search=API-SEARCH-001');

        $response->assertOk()
            ->assertJsonFragment([
                'employee_code' => 'API-SEARCH-001',
            ])
            ->assertJsonMissing([
                'employee_code' => 'API-OTHER-002',
            ]);
    }

    public function test_employee_api_rejects_invalid_per_page_value(): void
    {
        $this->authenticateAdmin();

        $response = $this->getJson('/api/employees?per_page=0');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page']);
    }

    public function test_department_api_returns_only_active_departments(): void
    {
        $this->authenticateAdmin();

        Department::factory()->create([
            'name' => 'Active API Department',
            'status' => 'active',
        ]);

        Department::factory()->create([
            'name' => 'Inactive API Department',
            'status' => 'inactive',
        ]);

        $response = $this->getJson('/api/departments');

        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => ['id', 'name'],
                ],
            ])
            ->assertJsonFragment([
                'name' => 'Active API Department',
            ])
            ->assertJsonMissing([
                'name' => 'Inactive API Department',
            ])
            ->assertJsonCount(1, 'data');
    }
}
