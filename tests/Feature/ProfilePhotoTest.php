<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private_photos');
    }

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }

    private function createEmployeeWithAccount(): array
    {
        $user = User::factory()->create([
            'role' => 'employee',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $employee = Employee::factory()->create([
            'user_id' => $user->getKey(),
            'profile_photo' => 'employee-photos/test-photo.jpg',
        ]);

        Storage::disk('private_photos')->put(
            'employee-photos/test-photo.jpg',
            UploadedFile::fake()->image('test-photo.jpg')->getContent()
        );

        return [$user, $employee];
    }

    public function test_admin_can_view_employee_photo(): void
    {
        $admin = $this->createAdmin();

        [, $employee] = $this->createEmployeeWithAccount();

        $this->actingAs($admin)
            ->get(route('profile-photos.employee', $employee))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_employee_can_view_own_photo(): void
    {
        [$user, $employee] = $this->createEmployeeWithAccount();

        $this->actingAs($user)
            ->get(route('profile-photos.employee', $employee))
            ->assertOk();
    }

    public function test_employee_cannot_view_another_employee_photo(): void
    {
        [$firstUser] = $this->createEmployeeWithAccount();

        [, $secondEmployee] = $this->createEmployeeWithAccount();

        $this->actingAs($firstUser)
            ->get(route('profile-photos.employee', $secondEmployee))
            ->assertForbidden();
    }

    public function test_guest_cannot_view_employee_photo(): void
    {
        [, $employee] = $this->createEmployeeWithAccount();

        $this->get(route('profile-photos.employee', $employee))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_view_standalone_user_photo(): void
    {
        $admin = $this->createAdmin();

        $user = User::factory()->create();

        $user->profile()->create([
            'name' => 'Standalone User',
            'profile_photo' => 'user-profile-photos/test-user.jpg',
        ]);

        Storage::disk('private_photos')->put(
            'user-profile-photos/test-user.jpg',
            UploadedFile::fake()->image('test-user.jpg')->getContent()
        );

        $this->actingAs($admin)
            ->get(route('profile-photos.user', $user))
            ->assertOk();
    }

    public function test_employee_cannot_view_another_users_photo(): void
    {
        $employee = User::factory()->create([
            'role' => 'employee',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $otherUser = User::factory()->create();

        $otherUser->profile()->create([
            'name' => 'Another User',
            'profile_photo' => 'user-profile-photos/test-user.jpg',
        ]);

        Storage::disk('private_photos')->put(
            'user-profile-photos/test-user.jpg',
            UploadedFile::fake()->image('test-user.jpg')->getContent()
        );

        $this->actingAs($employee)
            ->get(route('profile-photos.user', $otherUser))
            ->assertForbidden();
    }

    public function test_employee_cannot_upload_pdf_as_profile_photo(): void
    {
        [$user, $employee] = $this->createEmployeeWithAccount();

        $oldPhotoPath = $employee->profile_photo;

        $response = $this->actingAs($user)->put(
            route('employee.profile.update'),
            [
                'name' => $employee->name,
                'phone' => $employee->phone,
                'address' => $employee->address,
                'profile_photo' => UploadedFile::fake()->create(
                    'malicious.pdf',
                    100,
                    'application/pdf'
                ),
            ]
        );

        $response->assertSessionHasErrors('profile_photo');

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'profile_photo' => $oldPhotoPath,
        ]);

        Storage::disk('private_photos')->assertExists($oldPhotoPath);
    }
}
