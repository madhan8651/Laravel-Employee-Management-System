<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login page.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Authenticate using email or username.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $login = trim($validated['login']);

        $throttleKey = Str::transliterate(
            Str::lower($login) . '|' . $request->ip()
        );

        // Limit failed login attempts.
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'login' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        // Determine whether the identifier is an email or username.
        $loginField = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $user = User::where($loginField, $login)->first();

        // Reject nonexistent or inactive accounts.
        if (! $user || $user->status !== 'active') {
            RateLimiter::hit($throttleKey, 60);
            $this->logFailedLogin($request, $login);

            throw ValidationException::withMessages([
                'login' => 'The credentials are incorrect, or your account is inactive.',
            ]);
        }

        // Validate the password before revealing verification status.
        if (! Hash::check($validated['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            $this->logFailedLogin($request, $login);

            throw ValidationException::withMessages([
                'login' => 'The credentials are incorrect, or your account is inactive.',
            ]);
        }

        // Require email verification before login.
        if (! $user->hasVerifiedEmail()) {
            RateLimiter::hit($throttleKey, 60);
            $this->logFailedLogin($request, $login);

            throw ValidationException::withMessages([
                'login' => 'Please verify your email address before signing in. Check your inbox for the verification email.',
            ]);
        }

        // Authenticate the verified user.
        $credentials = [
            $loginField => $login,
            'password' => $validated['password'],
            'status' => 'active',
        ];

        if (! Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            RateLimiter::hit($throttleKey, 60);
            $this->logFailedLogin($request, $login);

            throw ValidationException::withMessages([
                'login' => 'The credentials are incorrect, or your account is inactive.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        // Prevent session fixation.
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Log out and invalidate the session.
     */
    /**
     * Record failed login attempts in the daily log.
     */
    private function logFailedLogin(Request $request, string $login): void
    {
        Log::channel('daily')->warning('Failed login attempt', [
            'login' => $login,
            'ip' => $request->ip(),
            'event' => 'failed_login',
        ]);
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
}



