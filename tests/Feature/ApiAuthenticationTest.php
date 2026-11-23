<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_active_user_can_login_and_receive_token(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Test Device',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Login successful.')
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonMissingPath('user.password')
            ->assertJsonMissingPath('user.remember_token');

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Test Device',
        ]);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
            'device_name' => 'Test Device',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'inactive',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Test Device',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_unverified_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Test Device',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        $token = $user->createToken('Test Device')->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/logout');

        $response->assertOk()
            ->assertJsonPath('message', 'Logout successful.');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_employee_can_only_view_their_own_employee_record(): void
    {
        $user = User::factory()->create([
            'role' => 'employee',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $otherUser = User::factory()->create([
            'role' => 'employee',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $ownEmployee = Employee::factory()->create([
            'user_id' => $user->id,
        ]);

        $otherEmployee = Employee::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/employees/{$ownEmployee->id}")
            ->assertOk();

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/employees/{$otherEmployee->id}")
            ->assertForbidden();
    }

    public function test_employee_cannot_create_an_employee_through_the_api(): void
    {
        $user = User::factory()->create([
            'role' => 'employee',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $department = Department::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/employees', [
                'employee_code' => 'API-TEST-001',
                'name' => 'Test Employee',
                'email' => 'api.employee@example.com',
                'designation' => 'Developer',
                'department_id' => $department->id,
                'status' => 'active',
            ])
            ->assertForbidden();
    }

    public function test_employee_cannot_change_another_employees_phone(): void
    {
        $user = User::factory()->create([
            'role' => 'employee',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $otherUser = User::factory()->create([
            'role' => 'employee',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $otherEmployee = Employee::factory()->create([
            'user_id' => $otherUser->id,
            'phone' => '9000000001',
        ]);

        $this->actingAs($user, 'sanctum')
            ->patchJson("/api/employees/{$otherEmployee->id}", [
                'phone' => '9000000002',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('employees', [
            'id' => $otherEmployee->id,
            'phone' => '9000000001',
        ]);
    }

    public function test_unauthenticated_requests_to_protected_api_routes_are_rejected(): void
    {
        $this->getJson('/api/employees')
            ->assertUnauthorized();

        $this->postJson('/api/logout')
            ->assertUnauthorized();
    }

    public function test_employee_cannot_fully_update_or_delete_employee_records(): void
    {
        $user = User::factory()->create([
            'role' => 'employee',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $employee = Employee::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user, 'sanctum')
            ->putJson("/api/employees/{$employee->id}", [
                'employee_code' => 'API-UPDATED-001',
                'name' => 'Unauthorized Update',
                'email' => 'unauthorized@example.com',
                'designation' => 'Manager',
                'department_id' => $employee->department_id,
                'status' => 'active',
            ])
            ->assertForbidden();

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/employees/{$employee->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'employee_code' => $employee->employee_code,
        ]);
    }

    public function test_admin_can_update_and_delete_employee_records(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $employee = Employee::factory()->create();

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/employees/{$employee->id}", [
                'employee_code' => 'API-ADMIN-001',
                'name' => 'Admin Updated Employee',
                'email' => 'admin.updated@example.com',
                'phone' => '9876543210',
                'designation' => 'Senior Developer',
                'department_id' => $employee->department_id,
                'status' => 'active',
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Admin Updated Employee');

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/employees/{$employee->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Employee deleted successfully.');

        $this->assertSoftDeleted('employees', [
            'id' => $employee->id,
        ]);
    }

    public function test_api_old_photo_cleanup_failure_does_not_delete_new_photo(): void
    {
        Storage::fake('private_photos');

        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $employee = Employee::factory()->create([
            'profile_photo' => 'employee-photos/old-photo.jpg',
        ]);

        $disk = Storage::disk('private_photos');
        $oldPhotoPath = $employee->profile_photo;

        $disk->put(
            $oldPhotoPath,
            UploadedFile::fake()->image('old-photo.jpg')->getContent()
        );

        $mockDisk = \Mockery::mock($disk)->makePartial();
        $mockDisk->shouldReceive('delete')
            ->once()
            ->with($oldPhotoPath)
            ->andThrow(new \RuntimeException('Simulated deletion failure'));

        Storage::shouldReceive('disk')
            ->with('private_photos')
            ->andReturn($mockDisk);

        $response = $this->actingAs($admin, 'sanctum')
            ->put("/api/employees/{$employee->id}", [
                'employee_code' => $employee->employee_code,
                'name' => 'API Updated Employee',
                'email' => $employee->email,
                'phone' => '9876543210',
                'designation' => 'Developer',
                'department_id' => $employee->department_id,
                'status' => 'active',
                'profile_photo' => UploadedFile::fake()
                    ->image('new-photo.jpg', 300, 300),
            ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'API Updated Employee');

        $employee->refresh();

        $this->assertNotSame($oldPhotoPath, $employee->profile_photo);
        $this->assertTrue($disk->exists($employee->profile_photo));
        $this->assertTrue($disk->exists($oldPhotoPath));
    }

    public function test_api_rejects_pdf_as_employee_profile_photo(): void
    {
        Storage::fake('private_photos');

        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $employee = Employee::factory()->create([
            'profile_photo' => 'employee-photos/existing.jpg',
        ]);

        Storage::disk('private_photos')->put(
            'employee-photos/existing.jpg',
            UploadedFile::fake()->image('existing.jpg')->getContent()
        );

        $oldPhotoPath = $employee->profile_photo;

        $response = $this->actingAs($admin, 'sanctum')
            ->put("/api/employees/{$employee->id}", [
                'employee_code' => $employee->employee_code,
                'name' => $employee->name,
                'email' => $employee->email,
                'phone' => '9876543210',
                'designation' => 'Developer',
                'department_id' => $employee->department_id,
                'status' => 'active',
                'profile_photo' => UploadedFile::fake()->create(
                    'malicious.pdf',
                    100,
                    'application/pdf'
                ),
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('profile_photo');

        $employee->refresh();

        $this->assertSame($oldPhotoPath, $employee->profile_photo);

        Storage::disk('private_photos')->assertExists($oldPhotoPath);
    }
}
