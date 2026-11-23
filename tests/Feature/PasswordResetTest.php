<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\PasswordSetupNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_forgot_password_form(): void
    {
        $this->get(route('password.request'))
            ->assertOk();
    }

    public function test_registered_user_can_request_password_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'reset@example.com',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => $user->email,
        ]);

        Notification::assertSentTo(
            $user,
            PasswordSetupNotification::class
        );
    }

    public function test_unknown_email_cannot_request_a_reset_link(): void
    {
        Notification::fake();

        $this->from(route('password.request'))
            ->post(route('password.email'), [
                'email' => 'unknown@example.com',
            ])
            ->assertRedirect(route('password.request'))
            ->assertSessionHasErrors('email');

        Notification::assertNothingSent();
    }

    public function test_password_reset_form_can_be_opened(): void
    {
        $user = User::factory()->create();

        $this->get(route('password.reset', [
            'token' => 'example-token',
            'email' => $user->email,
        ]))
            ->assertOk();
    }

    public function test_valid_reset_token_changes_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
            'email_verified_at' => now(),
        ]);

        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $this->assertTrue(
            Hash::check(
                'NewPassword123!',
                $user->fresh()->password
            )
        );

        $this->assertFalse(
            Hash::check(
                'OldPassword123!',
                $user->fresh()->password
            )
        );
    }

    public function test_invalid_reset_token_does_not_change_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
        ]);

        $this->from(route('password.reset', [
            'token' => 'invalid-token',
            'email' => $user->email,
        ]))
            ->post(route('password.update'), [
                'token' => 'invalid-token',
                'email' => $user->email,
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertTrue(
            Hash::check(
                'OldPassword123!',
                $user->fresh()->password
            )
        );
    }

    public function test_password_reset_requires_confirmation(): void
    {
        $user = User::factory()->create();

        $this->post(route('password.update'), [
            'token' => 'example-token',
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ])
            ->assertSessionHasErrors('password');
    }
}
