<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user_with_separate_profile(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Test Standalone User',
                'email' => 'standalone@example.com',
                'role' => 'admin',
            ])
            ->assertRedirect();

        $user = User::where('email', 'standalone@example.com')->firstOrFail();

        // Account information belongs in users.
        $this->assertDatabaseHas('users', [
            'id' => $user->getKey(),
            'email' => 'standalone@example.com',
            'role' => 'admin',
        ]);

        // Personal information belongs in user_profiles.
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->getKey(),
            'name' => 'Test Standalone User',
        ]);

        // The profile relationship should resolve correctly.
        $this->assertSame(
            'Test Standalone User',
            $user->profile()->first()->name
        );
    }

    public function test_admin_can_update_standalone_user_profile(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'email' => 'old@example.com',
        ]);

        $user->profile()->create([
            'name' => 'Old Profile Name',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                'name' => 'Updated Profile Name',
                'email' => 'updated@example.com',
            ])
            ->assertRedirect(route('admin.users.show', $user));

        $this->assertDatabaseHas('users', [
            'id' => $user->getKey(),
            'email' => 'updated@example.com',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->getKey(),
            'name' => 'Updated Profile Name',
        ]);
    }

    public function test_admin_updates_employee_name_in_employee_record(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'role' => 'employee',
            'email' => 'employee@example.com',
        ]);

        $employee = \App\Models\Employee::factory()->create([
            'user_id' => $user->getKey(),
            'name' => 'Old Employee Name',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                'name' => 'Updated Employee Name',
                'email' => 'updated.employee@example.com',
            ])
            ->assertRedirect(route('admin.users.show', $user));

        $this->assertDatabaseHas('employees', [
            'id' => $employee->getKey(),
            'name' => 'Updated Employee Name',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->getKey(),
            'email' => 'updated.employee@example.com',
        ]);

        $this->assertDatabaseMissing('user_profiles', [
            'user_id' => $user->getKey(),
        ]);
    }

        public function test_admin_cannot_update_user_with_password_missing_uppercase(): void
        {
            $admin = User::factory()->create([
                'role' => 'admin',
                'status' => 'active',
            ]);

            $user = User::factory()->create([
                'role' => 'employee',
                'password' => bcrypt('OldPassword123!'),
            ]);

            $this->actingAs($admin)
                ->put(route('admin.users.update', $user), [
                    'name' => 'Test Employee',
                    'email' => $user->email,
                    'password' => 'newpassword123',
                    'password_confirmation' => 'newpassword123',
                ])
                ->assertSessionHasErrors('password');

            $this->assertTrue(
                \Illuminate\Support\Facades\Hash::check(
                    'OldPassword123!',
                    $user->fresh()->password
                )
            );
        }

        public function test_admin_cannot_update_user_with_password_missing_lowercase(): void
        {
            $admin = User::factory()->create([
                'role' => 'admin',
                'status' => 'active',
            ]);

            $user = User::factory()->create([
                'role' => 'employee',
                'password' => bcrypt('OldPassword123!'),
            ]);

            $this->actingAs($admin)
                ->put(route('admin.users.update', $user), [
                    'name' => 'Test Employee',
                    'email' => $user->email,
                    'password' => 'NEWPASSWORD123',
                    'password_confirmation' => 'NEWPASSWORD123',
                ])
                ->assertSessionHasErrors('password');

            $this->assertTrue(
                \Illuminate\Support\Facades\Hash::check(
                    'OldPassword123!',
                    $user->fresh()->password
                )
            );
        }

        public function test_admin_cannot_update_user_with_password_missing_number(): void
        {
            $admin = User::factory()->create([
                'role' => 'admin',
                'status' => 'active',
            ]);

            $user = User::factory()->create([
                'role' => 'employee',
                'password' => bcrypt('OldPassword123!'),
            ]);

            $this->actingAs($admin)
                ->put(route('admin.users.update', $user), [
                    'name' => 'Test Employee',
                    'email' => $user->email,
                    'password' => 'NewPasswordABC',
                    'password_confirmation' => 'NewPasswordABC',
                ])
                ->assertSessionHasErrors('password');

            $this->assertTrue(
                \Illuminate\Support\Facades\Hash::check(
                    'OldPassword123!',
                    $user->fresh()->password
                )
            );
        }

        public function test_admin_can_delete_standalone_user_and_profile(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $user->profile()->create([
            'name' => 'Standalone User',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $user->getKey(),
        ]);

        $this->assertDatabaseMissing('user_profiles', [
            'user_id' => $user->getKey(),
        ]);
    }

    public function test_admin_can_delete_employee_account_and_preserve_employee_record(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'role' => 'employee',
        ]);

        $employee = \App\Models\Employee::factory()->create([
            'user_id' => $user->getKey(),
            'name' => 'Employee History',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $user->getKey(),
        ]);

        // Employee record must remain in the database.
        $this->assertDatabaseHas('employees', [
            'id' => $employee->getKey(),
            'user_id' => null,
        ]);

        // Employee record should be soft-deleted.
        $this->assertSoftDeleted('employees', [
            'id' => $employee->getKey(),
        ]);
    }
}