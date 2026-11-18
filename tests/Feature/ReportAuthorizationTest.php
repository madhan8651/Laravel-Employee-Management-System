<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->role = 'admin';
        $admin->save();

        return $admin;
    }

    private function createEmployee(): User
    {
        $employee = User::factory()->create();
        $employee->role = 'employee';
        $employee->save();

        return $employee;
    }

    public function test_guest_cannot_access_reports(): void
    {
        $response = $this->get(route('admin.reports.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_employee_cannot_access_reports(): void
    {
        $employee = $this->createEmployee();

        $response = $this->actingAs($employee)
            ->get(route('admin.reports.index'));

        $response->assertForbidden();
    }

    public function test_employee_cannot_export_employee_csv(): void
    {
        $employee = $this->createEmployee();

        $response = $this->actingAs($employee)
            ->get(route('admin.reports.export'));

        $response->assertForbidden();
    }

    public function test_admin_can_access_reports(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.index'));

        $response->assertOk();
    }
}
