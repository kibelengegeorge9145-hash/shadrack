<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->withCount(['roles'])
            ->orderByRaw('FIELD(role, "admin", "accountant", "teacher", "parent", "student")')
            ->orderBy('first_name')
            ->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    public function resetPassword(Request $request, User $user)
    {
        $temporaryPassword = $this->generateTemporaryPassword();

        $user->forceFill([
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
        ])->save();

        AuditLogService::log('password_reset_by_admin', "Temporary password issued for {$user->full_name} ({$user->email})", $user);

        return redirect()->route('admin.users.index')
            ->with('temp_password', [
                'user' => $user->full_name,
                'password' => $temporaryPassword,
            ])
            ->with('success', 'Password reset complete. Give the user the temporary password shown below.');
    }

    protected function generateTemporaryPassword(): string
    {
        return 'Temp@'.strtoupper(Str::random(3)).random_int(1000, 9999);
    }
}
