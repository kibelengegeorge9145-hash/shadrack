<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function showProfile()
    {
        return view('account.profile');
    }

    public function showChangePasswordForm()
    {
        return view('account.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();

        $user->forceFill([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ])->save();

        AuditLogService::log('password_change', $user->full_name.' changed their password');

        return redirect($this->homeFor($user))->with('success', 'Your password has been updated.');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $user = $request->user();
        $user->update($request->only('phone', 'email'));

        AuditLogService::log('profile_update', $user->full_name.' updated their profile');

        return back()->with('success', 'Profile updated.');
    }

    protected function homeFor($user): string
    {
        return match ($user->role) {
            'admin' => route('admin.dashboard'),
            'accountant' => route('accountant.dashboard'),
            'teacher' => route('teacher.dashboard'),
            'parent' => route('parent.dashboard'),
            default => route('student.dashboard'),
        };
    }
}
