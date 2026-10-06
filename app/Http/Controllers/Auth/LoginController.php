<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect($this->homeFor(auth()->user()));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = trim((string) $request->identifier);

        $this->ensureNotRateLimited($identifier);

        $user = User::findForLogin($identifier);

        if (! $user) {
            RateLimiter::hit($this->throttleKey($identifier), 60);

            throw ValidationException::withMessages([
                'identifier' => __('These credentials do not match our records.'),
            ]);
        }

        if (! $user->is_active) {
            RateLimiter::hit($this->throttleKey($identifier), 60);

            throw ValidationException::withMessages([
                'identifier' => 'This account has been deactivated.',
            ]);
        }

        if (! Auth::attempt(['id' => $user->id, 'password' => $request->password], $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($identifier), 60);

            throw ValidationException::withMessages([
                'identifier' => __('These credentials do not match our records.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($identifier));

        $request->session()->regenerate();

        $user->update(['last_login_at' => now()]);

        AuditLogService::log('login', $user->full_name.' logged in.');

        return redirect()->intended($this->homeFor($user));
    }

    public function logout(Request $request)
    {
        AuditLogService::log('logout', auth()->user()?->full_name.' logged out.');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function homeFor(User $user): string
    {
        return match ($user->role) {
            User::ROLE_ADMIN => route('admin.dashboard'),
            User::ROLE_ACCOUNTANT => route('accountant.dashboard'),
            User::ROLE_TEACHER => route('teacher.dashboard'),
            User::ROLE_PARENT => route('parent.dashboard'),
            User::ROLE_STUDENT => route('student.dashboard'),
            default => route('student.dashboard'),
        };
    }

    protected function throttleKey(string $identifier): string
    {
        return 'login:'.strtolower($identifier).'|'.request()->ip();
    }

    protected function ensureNotRateLimited(string $identifier): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($identifier), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($identifier));

        throw ValidationException::withMessages([
            'identifier' => 'Too many login attempts. Please try again in '.ceil($seconds / 60).' minute(s).',
        ]);
    }
}