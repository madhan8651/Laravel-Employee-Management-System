<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;

class Module7ValidationTest extends TestCase
{
    use RefreshDatabase;
public function test_valid_employee_profile_photo_is_uploaded(): void
{
    Storage::fake('private_photos');

    $admin = $this->createAdmin();
    $data = $this->validEmployeeData();

    $data['profile_photo'] = UploadedFile::fake()
        ->image('employee.jpg', 200, 200);

    $response = $this->actingAs($admin)
        ->post(route('admin.employees.store'), $data);

    $response->assertSessionHasNoErrors();

    $employee = Employee::where(
        'employee_code',
        'EMP001'
    )->firstOrFail();

    $this->assertNotEmpty($employee->getAttribute('profile_photo'));

    Storage::disk('private_photos')->assertExists(
        $employee->getAttribute('profile_photo')
    );
}

    private function createAdmin(): User
    {
        $admin = User::factory()->create();

        // Role is not mass assignable, so set it explicitly.
        $admin->role = 'admin';
        $admin->save();

        return $admin;
    }

    private function validEmployeeData(): array
    {
        $department = Department::factory()->create();

        return [
            'employee_code' => 'EMP001',
            'name' => 'Test Employee',
            'email' => 'test.employee@example.com',
            'role' => 'employee',
            'designation' => 'Software Engineer',
            'phone' => '9876543210',
            'department_id' => $department->getKey(),
            'gender' => 'male',
            'status' => 'active',
            'date_of_birth' => '1998-05-15',
            'date_of_joining' => '2024-01-15',
            'salary' => '25000.00',
            'address' => 'Test Address',
        ];
    }

    public function test_employee_request_rejects_invalid_email(): void
    {
        $admin = $this->createAdmin();

        $data = $this->validEmployeeData();
        $data['email'] = 'not-an-email';

        $response = $this->actingAs($admin)
            ->post(route('admin.employees.store'), $data);

        $response->assertSessionHasErrors('email');

        $this->assertDatabaseMissing('employees', [
            'employee_code' => 'EMP001',
        ]);
    }

    public function test_employee_request_rejects_negative_salary(): void
    {
        $admin = $this->createAdmin();

        $data = $this->validEmployeeData();
        $data['salary'] = '-100';

        $response = $this->actingAs($admin)
            ->post(route('admin.employees.store'), $data);

        $response->assertSessionHasErrors('salary');

        $this->assertDatabaseMissing('employees', [
            'employee_code' => 'EMP001',
        ]);
    }

    public function test_active_employee_requires_department(): void
    {
        $admin = $this->createAdmin();

        $data = $this->validEmployeeData();
        $data['department_id'] = '';
        
        $response = $this->actingAs($admin)
            ->post(route('admin.employees.store'), $data);

        $response->assertSessionHasErrors('department_id');

        $this->assertDatabaseMissing('employees', [
            'employee_code' => 'EMP001',
        ]);
    }

public function test_duplicate_employee_code_is_rejected(): void
{
    $admin = $this->createAdmin();
    $data = $this->validEmployeeData();

    $this->actingAs($admin)
        ->post(route('admin.employees.store'), $data);

    $duplicate = $this->validEmployeeData();
    $duplicate['email'] = 'another.employee@example.com';

    $response = $this->actingAs($admin)
        ->post(route('admin.employees.store'), $duplicate);

    $response->assertSessionHasErrors('employee_code');
}

public function test_duplicate_employee_email_is_rejected(): void
{
    $admin = $this->createAdmin();
    $data = $this->validEmployeeData();

    $this->actingAs($admin)
        ->post(route('admin.employees.store'), $data);

    $duplicate = $this->validEmployeeData();
    $duplicate['employee_code'] = 'EMP002';

    $response = $this->actingAs($admin)
        ->post(route('admin.employees.store'), $duplicate);

    $response->assertSessionHasErrors('email');
}

public function test_employee_under_18_is_rejected(): void
{
    $admin = $this->createAdmin();

    $data = $this->validEmployeeData();
    $data['date_of_birth'] = now()->subYears(17)->toDateString();

    $response = $this->actingAs($admin)
        ->post(route('admin.employees.store'), $data);

    $response->assertSessionHasErrors('date_of_birth');
}

public function test_joining_date_before_birth_date_is_rejected(): void
{
    $admin = $this->createAdmin();

    $data = $this->validEmployeeData();
    $data['date_of_birth'] = '1998-05-15';
    $data['date_of_joining'] = '1997-01-01';

    $response = $this->actingAs($admin)
        ->post(route('admin.employees.store'), $data);

    $response->assertSessionHasErrors('date_of_joining');
}

public function test_invalid_employee_profile_photo_is_rejected(): void
{
    Storage::fake('private_photos');

    $admin = $this->createAdmin();
    $data = $this->validEmployeeData();

    $data['profile_photo'] = UploadedFile::fake()
        ->create('employee.pdf', 100, 'application/pdf');

    $response = $this->actingAs($admin)
        ->post(route('admin.employees.store'), $data);

    $response->assertSessionHasErrors('profile_photo');

    $this->assertDatabaseMissing('employees', [
        'employee_code' => 'EMP001',
    ]);
}

public function test_guest_cannot_access_employee_index(): void
{
    $response = $this->get(route('admin.employees.index'));

    $response->assertRedirect(route('login'));
}

public function test_non_admin_cannot_create_employee(): void
{
    $user = User::factory()->create();

    $user->role = 'employee';
    $user->save();

    $response = $this->actingAs($user)
        ->post(route('admin.employees.store'), $this->validEmployeeData());

    $response->assertForbidden();

    $this->assertDatabaseMissing('employees', [
        'employee_code' => 'EMP001',
    ]);
}

public function test_forged_user_id_is_ignored_when_creating_employee(): void
{
    Notification::fake();

    $admin = $this->createAdmin();

    // An existing account that the request must not hijack.
    $existingUser = User::factory()->create([
        'email' => 'existing@example.com',
    ]);

    $data = $this->validEmployeeData();

    // Attempt to link the new employee to another user's account.
    $data['user_id'] = $existingUser->getKey();

    $this->actingAs($admin)
        ->post(route('admin.employees.store'), $data)
        ->assertSessionHasNoErrors();

    $employee = Employee::where(
        'employee_code',
        'EMP001'
    )->firstOrFail();

    // The forged user ID must not be used.
    $this->assertNotSame(
        $existingUser->getKey(),
        $employee->getAttribute('user_id')
    );

    // The employee must be linked to the newly created account.
    $this->assertDatabaseHas('users', [
        'id' => $employee->getAttribute('user_id'),
        'email' => 'test.employee@example.com',
    ]);
}

public function test_employee_can_keep_own_code_and_email_when_updated(): void
{
    $admin = $this->createAdmin();
    $data = $this->validEmployeeData();

    $this->actingAs($admin)
        ->post(route('admin.employees.store'), $data);

    $employee = Employee::where(
        'employee_code',
        'EMP001'
    )->firstOrFail();

    $updateData = $this->validEmployeeData();
    $updateData['name'] = 'Updated Test Employee';

    $response = $this->actingAs($admin)
        ->put(route('admin.employees.update', $employee), $updateData);

    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('employees', [
        'id' => $employee->getKey(),
        'employee_code' => 'EMP001',
        'email' => 'test.employee@example.com',
        'name' => 'Updated Test Employee',
    ]);
}

public function test_update_rejects_another_employees_email(): void
{
    $admin = $this->createAdmin();

    $firstData = $this->validEmployeeData();

    $this->actingAs($admin)
        ->post(route('admin.employees.store'), $firstData);

    $secondData = $this->validEmployeeData();
    $secondData['employee_code'] = 'EMP002';
    $secondData['email'] = 'second.employee@example.com';

    $this->actingAs($admin)
        ->post(route('admin.employees.store'), $secondData);

    $employee = Employee::where(
        'employee_code',
        'EMP002'
    )->firstOrFail();

    $updateData = $this->validEmployeeData();
    $updateData['employee_code'] = 'EMP002';
    $updateData['email'] = 'test.employee@example.com';

    $response = $this->actingAs($admin)
        ->put(route('admin.employees.update', $employee), $updateData);

    $response->assertSessionHasErrors('email');
}

public function test_updating_employee_replaces_old_profile_photo(): void
{
    Storage::fake('private_photos');

    $admin = $this->createAdmin();
    $data = $this->validEmployeeData();

    $data['profile_photo'] = UploadedFile::fake()
        ->image('old-photo.jpg', 200, 200);

    $this->actingAs($admin)
        ->post(route('admin.employees.store'), $data);

    $employee = Employee::where(
        'employee_code',
        'EMP001'
    )->firstOrFail();

    $oldPhotoPath = $employee->getAttribute('profile_photo');

    $this->assertNotEmpty($oldPhotoPath);
    Storage::disk('private_photos')->assertExists($oldPhotoPath);

    $updateData = $this->validEmployeeData();
    $updateData['profile_photo'] = UploadedFile::fake()
        ->image('new-photo.jpg', 300, 300);

    $response = $this->actingAs($admin)
        ->put(
            route('admin.employees.update', $employee),
            $updateData
        );

    $response->assertSessionHasNoErrors();

    $employee->refresh();

    $this->assertNotEmpty($employee->getAttribute('profile_photo'));
    $this->assertNotSame($oldPhotoPath, $employee->getAttribute('profile_photo'));

    Storage::disk('private_photos')->assertMissing($oldPhotoPath);
    Storage::disk('private_photos')->assertExists($employee->getAttribute('profile_photo'));
}

public function test_old_photo_cleanup_failure_does_not_delete_new_employee_photo(): void
{
    Storage::fake('private_photos');

    $admin = $this->createAdmin();
    $data = $this->validEmployeeData();

    $data['profile_photo'] = UploadedFile::fake()
        ->image('old-photo.jpg', 200, 200);

    $this->actingAs($admin)
        ->post(route('admin.employees.store'), $data);

    $employee = Employee::where(
        'employee_code',
        'EMP001'
    )->firstOrFail();

    $oldPhotoPath = $employee->getAttribute('profile_photo');

    $disk = Storage::disk('private_photos');
    $mockDisk = \Mockery::mock($disk)->makePartial();

    $mockDisk->shouldReceive('delete')
        ->once()
        ->with($oldPhotoPath)
        ->andThrow(new \RuntimeException('Simulated old photo deletion failure'));

    Storage::shouldReceive('disk')
        ->with('private_photos')
        ->andReturn($mockDisk);

    $updateData = $this->validEmployeeData();
    $updateData['profile_photo'] = UploadedFile::fake()
        ->image('new-photo.jpg', 300, 300);

    $response = $this->actingAs($admin)
        ->put(
            route('admin.employees.update', $employee),
            $updateData
        );

    $employee->refresh();

    $this->assertNotSame($oldPhotoPath, $employee->getAttribute('profile_photo'));

    Storage::disk('private_photos')->assertExists(
        $employee->getAttribute('profile_photo')
    );
}

public function test_updating_employee_without_new_photo_preserves_old_photo(): void
{
    Storage::fake('private_photos');

    $admin = $this->createAdmin();
    $data = $this->validEmployeeData();

    $data['profile_photo'] = UploadedFile::fake()
        ->image('existing-photo.jpg', 200, 200);

    $this->actingAs($admin)
        ->post(route('admin.employees.store'), $data);

    $employee = Employee::where(
        'employee_code',
        'EMP001'
    )->firstOrFail();

    $oldPhotoPath = $employee->getAttribute('profile_photo');

    $this->assertNotEmpty($oldPhotoPath);
    Storage::disk('private_photos')->assertExists($oldPhotoPath);

    $updateData = $this->validEmployeeData();
    $updateData['name'] = 'Employee Without New Photo';

    $response = $this->actingAs($admin)
        ->put(
            route('admin.employees.update', $employee),
            $updateData
        );

    $response->assertSessionHasNoErrors();

    $employee->refresh();

    $this->assertSame($oldPhotoPath, $employee->getAttribute('profile_photo'));
    Storage::disk('private_photos')->assertExists($oldPhotoPath);
}

public function test_photo_cleanup_failure_does_not_report_employee_deletion_as_failed(): void
{
    Storage::fake('private_photos');

    $admin = $this->createAdmin();
    $data = $this->validEmployeeData();

    $data['profile_photo'] = UploadedFile::fake()
        ->image('delete-photo.jpg', 200, 200);

    $this->actingAs($admin)
        ->post(route('admin.employees.store'), $data);

    $employee = Employee::where(
        'employee_code',
        'EMP001'
    )->firstOrFail();

    $employeeId = $employee->getAttribute('id');
    $userId = $employee->getAttribute('user_id');
    $photoPath = $employee->getAttribute('profile_photo');

    $this->assertNotEmpty($userId);
    Storage::disk('private_photos')->assertExists($photoPath);

    $disk = Storage::disk('private_photos');
    $mockDisk = \Mockery::mock($disk)->makePartial();

    $mockDisk->shouldReceive('exists')
        ->with($photoPath)
        ->andReturn(true);

    $mockDisk->shouldReceive('delete')
        ->once()
        ->with($photoPath)
        ->andThrow(
            new \RuntimeException('Simulated photo deletion failure')
        );

    Storage::shouldReceive('disk')
        ->with('private_photos')
        ->andReturn($mockDisk);

    $response = $this->actingAs($admin)
        ->delete(route('admin.employees.destroy', $employee));

    $response->assertSessionHas(
        'success',
        'Employee and linked user account deleted successfully.'
    );

    $this->assertSoftDeleted('employees', [
        'id' => $employeeId,
    ]);

    $this->assertDatabaseMissing('users', [
        'id' => $userId,
    ]);

    Storage::disk('private_photos')->assertExists($photoPath);
}

}

