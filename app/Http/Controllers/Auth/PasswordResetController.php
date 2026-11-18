<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController extends Controller
{
    /**
     * Show the forgot-password form.
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a password setup/reset link to the user's email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->withInput($request->only('email'));
    }

    /**
     * Display the password setup/reset form.
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Save the new password and send an email verification link.
     */
    public function reset(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(12)
                    ->mixedCase()
                    ->numbers(),
            ],
        ]);

        $resetUser = null;

        $status = Password::reset(
            $validated,
            function ($user, string $password) use (&$resetUser) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));

                $resetUser = $user;
            }
        );

        // Stop if the token is invalid or expired.
        if ($status !== Password::PASSWORD_RESET || ! $resetUser) {
            return back()
                ->withErrors(['email' => __($status)])
                ->withInput($request->only('email'));
        }

        // Send verification email only after the password is saved.
        if (! $resetUser->hasVerifiedEmail()) {
            try {
                $resetUser->sendEmailVerificationNotification();
            } catch (\Throwable $exception) {
                Log::error(
                    'Email verification notification failed after password setup.',
                    [
                        'user_id' => $resetUser->id,
                        'exception' => $exception->getMessage(),
                    ]
                );

                return redirect()
                    ->route('login')
                    ->with(
                        'warning',
                        'Your password was set successfully, but we could not send the verification email. Please contact the administrator.'
                    );
            }

            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'Your password has been set successfully. A verification email has been sent to your registered email address. Please verify your email before signing in.'
                );
        }

        // The email is already verified, so no new verification email is needed.
        return redirect()
            ->route('login')
            ->with(
                'success',
                'Your password has been reset successfully. You can now sign in.'
            );
    }
}
