<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        return $admin;
    }

    private function createEmployee(): User
    {
        return User::factory()->create([
            'role' => 'employee',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }

    public function test_employee_cannot_access_admin_employee_list(): void
    {
        $this->actingAs($this->createEmployee())
            ->get(route('admin.employees.index'))
            ->assertForbidden();
    }

    public function test_employee_cannot_access_admin_user_list(): void
    {
        $this->actingAs($this->createEmployee())
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_employee_list(): void
    {
        $this->actingAs($this->createAdmin())
            ->get(route('admin.employees.index'))
            ->assertOk();
    }

    public function test_admin_can_access_admin_user_list(): void
    {
        $this->actingAs($this->createAdmin())
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_guest_cannot_access_admin_user_list(): void
    {
        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));
    }
}
