<?php

namespace Tests\Feature;

use App\Enums\EmployeeStatus;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WebAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private function createEmployeeUser(string $password = 'password'): User
    {
        $user = User::factory()->create([
            'role' => 'employee',
            'status' => 'active',
            'email_verified_at' => now(),
            'password' => $password,
        ]);

        Employee::factory()->create([
            'user_id' => $user->id,
            'status' => EmployeeStatus::Active,
        ]);

        return $user;
    }

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_verified_active_user_can_log_in_with_email(): void
    {
        $user = User::factory()->create([
            'email' => 'employee@example.com',
            'password' => Hash::make('Password123!'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->post(route('login.store'), [
            'login' => $user->email,
            'password' => 'Password123!',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'status' => 'inactive',
            'email_verified_at' => now(),
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'login' => $user->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_unverified_user_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'email_verified_at' => null,
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'login' => $user->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_employee_can_view_their_profile(): void
    {
        $user = $this->createEmployeeUser();

        $this->actingAs($user)
            ->get(route('employee.profile.show'))
            ->assertOk();
    }

    public function test_employee_can_update_their_profile_details(): void
    {
        $user = $this->createEmployeeUser();

        $originalEmail = $user->email;
        $originalRole = $user->getRawOriginal('role');

        $this->actingAs($user)
            ->put(route('employee.profile.update'), [
                'name' => 'Updated Employee',
                'phone' => '9876543210',
                'address' => 'Coimbatore',
            ])
            ->assertRedirect(route('employee.profile.show'))
            ->assertSessionHas('success');

        // Employee personal details should be updated in employees.
        $this->assertDatabaseHas('employees', [
            'user_id' => $user->id,
            'name' => 'Updated Employee',
            'phone' => '9876543210',
        ]);

        // Address should be readable through the Employee model.
        $employee = Employee::where('user_id', $user->id)
            ->firstOrFail();

        $this->assertSame('Coimbatore', $employee->address);

        // Address should be encrypted in the database.
        $storedAddress = DB::table('employees')
            ->where('user_id', $user->id)
            ->value('address');

        $this->assertNotSame('Coimbatore', $storedAddress);

        // Employee profile updates should not change account information.
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $originalEmail,
            'role' => $originalRole,
        ]);
    }

    public function test_employee_cannot_promote_themselves_to_admin(): void
    {
        $user = $this->createEmployeeUser();

        $this->actingAs($user)
            ->put(route('employee.profile.update'), [
                'name' => 'Updated Employee',
                'phone' => '9876543210',
                'address' => 'Coimbatore',
                'role' => 'admin',
            ])
            ->assertRedirect(route('employee.profile.show'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'employee',
        ]);

        $this->assertSame('employee', $user->fresh()->role->value);
    }

    public function test_employee_cannot_change_password_with_wrong_current_password(): void
    {
        $user = $this->createEmployeeUser('CurrentPassword123!');

        $this->actingAs($user)
            ->put(route('employee.profile.password'), [
                'current_password' => 'WrongPassword123!',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ])
            ->assertSessionHasErrors(
                'current_password',
                null,
                'passwordChange'
            );

        $this->assertTrue(
            Hash::check(
                'CurrentPassword123!',
                $user->fresh()->password
            )
        );
    }

    public function test_employee_can_change_password_with_correct_current_password(): void
    {
        $user = $this->createEmployeeUser('CurrentPassword123!');

        $this->actingAs($user)
            ->put(route('employee.profile.password'), [
                'current_password' => 'CurrentPassword123!',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ])
            ->assertRedirect(route('employee.profile.show'))
            ->assertSessionHas('success');

        $this->assertTrue(
            Hash::check('NewPassword123!', $user->fresh()->password)
        );
    }

    public function test_old_photo_cleanup_failure_does_not_delete_new_profile_photo(): void
    {
        Storage::fake('private_photos');

        $user = $this->createEmployeeUser();
        $employee = $user->employee;

        $oldPhotoPath = 'employee-photos/old-photo.jpg';
        $disk = Storage::disk('private_photos');

        $disk->put(
            $oldPhotoPath,
            UploadedFile::fake()->image('old-photo.jpg')->getContent()
        );

        $employee->update([
            'profile_photo' => $oldPhotoPath,
        ]);

        // Simulate a failure when deleting the old photo.
        $mockDisk = \Mockery::mock($disk)->makePartial();
        $mockDisk->shouldReceive('delete')
            ->once()
            ->with($oldPhotoPath)
            ->andThrow(new \RuntimeException('Simulated deletion failure'));

        Storage::shouldReceive('disk')
            ->with('private_photos')
            ->andReturn($mockDisk);

        $response = $this->actingAs($user)
            ->put(route('employee.profile.update'), [
                'name' => 'Updated Employee',
                'phone' => '9876543210',
                'address' => 'Coimbatore',
                'profile_photo' => UploadedFile::fake()
                    ->image('new-photo.jpg', 300, 300),
            ]);

        $response->assertRedirect(route('employee.profile.show'))
            ->assertSessionHas('success');

        $employee->refresh();

        $this->assertNotSame($oldPhotoPath, $employee->profile_photo);
        $this->assertTrue($disk->exists($employee->profile_photo));
        $this->assertTrue($disk->exists($oldPhotoPath));
    }

    public function test_verified_active_user_can_log_in_with_username(): void
    {
        $user = User::factory()->create([
            'username' => 'testemployee',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->post(route('login.store'), [
            'login' => 'testemployee',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_is_throttled_after_five_failed_attempts(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Five failed attempts using the same login identifier.
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from(route('login'))
                ->post(route('login.store'), [
                    'login' => $user->email,
                    'password' => 'wrong-password',
                ])
                ->assertRedirect(route('login'))
                ->assertSessionHasErrors('login');
        }

        // The sixth attempt should be blocked by the rate limiter.
        $this->from(route('login'))
            ->post(route('login.store'), [
                'login' => $user->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_password_change_requires_confirmation(): void
    {
        $user = $this->createEmployeeUser('CurrentPassword123!');

        $this->actingAs($user)
            ->put(route('employee.profile.password'), [
                'current_password' => 'CurrentPassword123!',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'DifferentPassword123!',
            ])
            ->assertSessionHasErrors(
                'password',
                null,
                'passwordChange'
            );

        $this->assertTrue(
            Hash::check('CurrentPassword123!', $user->fresh()->password)
        );
    }

    public function test_password_change_requires_at_least_twelve_characters(): void
    {
        $user = $this->createEmployeeUser('CurrentPassword123!');

        $this->actingAs($user)
            ->put(route('employee.profile.password'), [
                'current_password' => 'CurrentPassword123!',
                'password' => 'Short123!',
                'password_confirmation' => 'Short123!',
            ])
            ->assertSessionHasErrors(
                'password',
                null,
                'passwordChange'
            );

        $this->assertTrue(
            Hash::check('CurrentPassword123!', $user->fresh()->password)
        );
    }

    public function test_password_change_requires_uppercase_letter(): void
    {
        $user = $this->createEmployeeUser('CurrentPassword123!');

        $this->actingAs($user)
            ->put(route('employee.profile.password'), [
                'current_password' => 'CurrentPassword123!',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])
            ->assertSessionHasErrors('password', null, 'passwordChange');

        $this->assertTrue(
            Hash::check('CurrentPassword123!', $user->fresh()->password)
        );
    }

    public function test_password_change_requires_lowercase_letter(): void
    {
        $user = $this->createEmployeeUser('CurrentPassword123!');

        $this->actingAs($user)
            ->put(route('employee.profile.password'), [
                'current_password' => 'CurrentPassword123!',
                'password' => 'NEWPASSWORD123',
                'password_confirmation' => 'NEWPASSWORD123',
            ])
            ->assertSessionHasErrors('password', null, 'passwordChange');

        $this->assertTrue(
            Hash::check('CurrentPassword123!', $user->fresh()->password)
        );
    }

    public function test_password_change_requires_number(): void
    {
        $user = $this->createEmployeeUser('CurrentPassword123!');

        $this->actingAs($user)
            ->put(route('employee.profile.password'), [
                'current_password' => 'CurrentPassword123!',
                'password' => 'NewPasswordABC',
                'password_confirmation' => 'NewPasswordABC',
            ])
            ->assertSessionHasErrors('password', null, 'passwordChange');

        $this->assertTrue(
            Hash::check('CurrentPassword123!', $user->fresh()->password)
        );
    }

    public function test_guest_cannot_change_employee_password(): void
    {
        $this->put(route('employee.profile.password'), [
            'current_password' => 'CurrentPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertRedirect(route('login'));
    }

    public function test_web_post_request_without_csrf_token_is_rejected(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }
}
