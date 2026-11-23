<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchAndPaginationTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_can_search_employee_by_name(): void
    {
        $admin = $this->createAdmin();

        Employee::factory()->create([
            'name' => 'Test Search Employee',
        ]);

        Employee::factory()->create([
            'name' => 'Another Employee',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.employees.index', [
                'search' => 'Test Search Employee',
            ]));

        $response->assertOk()
            ->assertViewIs('admin.employees.index')
            ->assertSee('Test Search Employee')
            ->assertDontSee('Another Employee');
    }

    public function test_admin_can_search_employee_by_employee_code(): void
    {
        $admin = $this->createAdmin();

        Employee::factory()->create([
            'employee_code' => 'SEARCH-1001',
            'name' => 'Matching Employee',
        ]);

        Employee::factory()->create([
            'employee_code' => 'OTHER-2001',
            'name' => 'Different Employee',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.employees.index', [
                'search' => 'SEARCH-1001',
            ]));

        $response->assertOk()
            ->assertSee('Matching Employee')
            ->assertDontSee('Different Employee');
    }

    public function test_admin_can_search_department_by_name(): void
    {
        $admin = $this->createAdmin();

        Department::factory()->create([
            'name' => 'Quality Assurance',
        ]);

        Department::factory()->create([
            'name' => 'Human Resources',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.departments.index', [
                'search' => 'Quality Assurance',
            ]));

        $response->assertOk()
            ->assertViewIs('admin.departments.index')
            ->assertSee('Quality Assurance')
            ->assertDontSee('Human Resources');
    }

    public function test_employee_listing_paginates_records(): void
    {
        $admin = $this->createAdmin();

        Employee::factory()->count(12)->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.employees.index'));

        $response->assertOk()
            ->assertViewIs('admin.employees.index');

        $employees = $response->viewData('employees');

        $this->assertSame(10, $employees->count());
        $this->assertSame(12, $employees->total());
        $this->assertSame(1, $employees->currentPage());
    }

    public function test_department_listing_paginates_records(): void
    {
        $admin = $this->createAdmin();

        Department::factory()->count(12)->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.departments.index'));

        $response->assertOk()
            ->assertViewIs('admin.departments.index');

        $departments = $response->viewData('departments');

        $this->assertSame(10, $departments->count());
        $this->assertSame(12, $departments->total());
    }

    public function test_department_ajax_search_returns_json_html(): void
    {
        $admin = $this->createAdmin();

        Department::factory()->create([
            'name' => 'Information Technology',
        ]);

        $response = $this->actingAs($admin)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->getJson(route('admin.departments.index', [
                'search' => 'Information Technology',
            ]));

        $response->assertOk()
            ->assertJsonStructure(['html'])
            ->assertJsonFragment([
                'html' => $response->json('html'),
            ]);

        $this->assertStringContainsString(
            'Information Technology',
            $response->json('html')
        );
    }
}
