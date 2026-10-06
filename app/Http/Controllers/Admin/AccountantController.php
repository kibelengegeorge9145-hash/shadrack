<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accountant;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccountantController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $query = Accountant::latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('staff_id', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $accountants = $query->paginate(15)->withQueryString();

        return view('admin.accountants.index', compact('accountants'));
    }

    public function create()
    {
        return view('admin.accountants.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'staff_id' => ['nullable', 'string', 'max:255', 'unique:accountants,staff_id'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $staffId = $data['staff_id'] ?: $this->authService->nextStaffId('EBA');

        $user = User::create([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'name' => trim(implode(' ', array_filter([
                $data['first_name'], $data['middle_name'] ?? null, $data['last_name'],
            ]))),
            'username' => Str::slug($data['first_name'].'-'.$data['last_name'].'-'.Str::random(4), '-'),
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'role' => User::ROLE_ACCOUNTANT,
            'staff_id' => $staffId,
            'password' => $this->authService->temporaryPassword(),
            'must_change_password' => true,
            'is_active' => true,
        ]);

        $accountant = Accountant::create([
            'user_id' => $user->id,
            'staff_id' => $staffId,
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLogService::log('accountant_created', "Accountant {$accountant->full_name} ({$staffId}) created", $accountant);

        return redirect()->route('admin.accountants.index')
            ->with('success', "Accountant created successfully. Username: {$user->username}");
    }

    public function edit(Accountant $accountant)
    {
        return view('admin.accountants.edit', compact('accountant'));
    }

    public function update(Request $request, Accountant $accountant)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'staff_id' => ['required', 'string', 'max:255', 'unique:accountants,staff_id,'.$accountant->id],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $accountant->update($data);

        if ($accountant->user) {
            $accountant->user->update([
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'name' => trim(implode(' ', array_filter([
                    $data['first_name'], $data['middle_name'] ?? null, $data['last_name'],
                ]))),
                'staff_id' => $data['staff_id'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
            ]);

            if (! empty($data['password'])) {
                $accountant->user->update([
                    'password' => $data['password'],
                    'must_change_password' => false,
                ]);
            }
        }

        AuditLogService::log('accountant_updated', "Accountant {$accountant->full_name} ({$data['staff_id']}) updated", $accountant);

        return redirect()->route('admin.accountants.index')
            ->with('success', 'Accountant updated successfully.');
    }

    public function destroy(Accountant $accountant)
    {
        $name = $accountant->full_name;

        if ($accountant->user) {
            $accountant->user->update(['is_active' => false]);
        }

        $accountant->delete();

        AuditLogService::log('accountant_deleted', "Accountant {$name} removed", $accountant);

        return redirect()->route('admin.accountants.index')
            ->with('success', 'Accountant removed successfully.');
    }
}
