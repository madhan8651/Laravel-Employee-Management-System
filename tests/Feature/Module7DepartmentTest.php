<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Module7DepartmentTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->role = 'admin';
        $admin->save();

        return $admin;
    }

    public function test_guest_cannot_access_department_index(): void
    {
        $response = $this->get(route('admin.departments.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_create_department(): void
    {
        $user = User::factory()->create();
        $user->role = 'employee';
        $user->save();

        $response = $this->actingAs($user)->post(
            route('admin.departments.store'),
            [
                'name' => 'Quality Assurance',
                'description' => 'QA department',
            ]
        );

        $response->assertForbidden();

        $this->assertDatabaseMissing('departments', [
            'name' => 'Quality Assurance',
        ]);
    }

    public function test_duplicate_department_name_is_rejected(): void
    {
        $admin = $this->createAdmin();

        Department::factory()->create([
            'name' => 'Quality Assurance',
        ]);

        $response = $this->actingAs($admin)->post(
            route('admin.departments.store'),
            [
                'name' => 'Quality Assurance',
                'description' => 'Another QA department',
            ]
        );

        $response->assertSessionHasErrors('name');

        $this->assertSame(
            1,
            Department::where('name', 'Quality Assurance')->count()
        );
    }

    public function test_department_name_is_required(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(
            route('admin.departments.store'),
            [
                'name' => '',
                'description' => 'Department description',
            ]
        );

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseCount('departments', 0);
    }

    public function test_department_can_keep_its_existing_name_when_updated(): void
    {
        $admin = $this->createAdmin();

        $department = Department::factory()->create([
            'name' => 'Quality Assurance',
        ]);

        $response = $this->actingAs($admin)->put(
            route('admin.departments.update', $department),
            [
                'name' => 'Quality Assurance',
                'description' => 'Updated QA description',
            ]
        );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'Quality Assurance',
            'description' => 'Updated QA description',
        ]);
    }

public function test_department_description_over_1000_characters_is_rejected(): void
{
    $admin = $this->createAdmin();

    $response = $this->actingAs($admin)->post(
        route('admin.departments.store'),
        [
            'name' => 'Quality Assurance',
            'description' => str_repeat('A', 1001),
        ]
    );

    $response->assertSessionHasErrors('description');

    $this->assertDatabaseMissing('departments', [
        'name' => 'Quality Assurance',
    ]);
}

public function test_duplicate_department_name_is_rejected_during_update(): void
{
    $admin = $this->createAdmin();

    Department::factory()->create([
        'name' => 'Quality Assurance',
    ]);

    $department = Department::factory()->create([
        'name' => 'Human Resources',
    ]);

    $response = $this->actingAs($admin)->put(
        route('admin.departments.update', $department),
        [
            'name' => 'Quality Assurance',
            'description' => 'Updated description',
        ]
    );

    $response->assertSessionHasErrors('name');

    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
        'name' => 'Human Resources',
    ]);
}

public function test_non_admin_cannot_update_department(): void
{
    $user = User::factory()->create();
    $user->role = 'employee';
    $user->save();

    $department = Department::factory()->create([
        'name' => 'Quality Assurance',
    ]);

    $response = $this->actingAs($user)->put(
        route('admin.departments.update', $department),
        [
            'name' => 'Updated Department',
            'description' => 'Updated description',
        ]
    );

    $response->assertForbidden();

    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
        'name' => 'Quality Assurance',
    ]);
}

public function test_non_admin_cannot_delete_department(): void
{
    $user = User::factory()->create();
    $user->role = 'employee';
    $user->save();

    $department = Department::factory()->create([
        'name' => 'Quality Assurance',
    ]);

    $response = $this->actingAs($user)->delete(
        route('admin.departments.destroy', $department)
    );

    $response->assertForbidden();

    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
        'name' => 'Quality Assurance',
    ]);
}
}
