<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MyParent;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ParentController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $query = MyParent::withCount('students')->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $parents = $query->paginate(15)->withQueryString();

        return view('admin.parents.index', compact('parents'));
    }

    public function create()
    {
        return view('admin.parents.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'relationship' => ['required', 'in:father,mother,guardian'],
        ]);

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
            'role' => User::ROLE_PARENT,
            'password' => $this->authService->temporaryPassword(),
            'must_change_password' => true,
            'is_active' => true,
        ]);

        $parent = MyParent::create([
            'user_id' => $user->id,
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'relationship' => $data['relationship'],
        ]);

        AuditLogService::log('parent_created', "Parent {$parent->full_name} created", $parent);

        return redirect()->route('admin.parents.index')
            ->with('success', "Parent created successfully. Username: {$user->username}");
    }

    public function show(MyParent $parent)
    {
        return redirect()->route('admin.parents.edit', $parent);
    }

    public function edit(MyParent $parent)
    {
        return view('admin.parents.edit', compact('parent'));
    }

    public function update(Request $request, MyParent $parent)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'relationship' => ['required', 'in:father,mother,guardian'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $parent->update($data);

        if ($parent->user) {
            $parent->user->update([
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'name' => trim(implode(' ', array_filter([
                    $data['first_name'], $data['middle_name'] ?? null, $data['last_name'],
                ]))),
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
            ]);

            if (! empty($data['password'])) {
                $parent->user->update([
                    'password' => $data['password'],
                    'must_change_password' => false,
                ]);
            }
        }

        AuditLogService::log('parent_updated', "Parent {$parent->full_name} updated", $parent);

        return redirect()->route('admin.parents.index')
            ->with('success', 'Parent updated successfully.');
    }

    public function destroy(MyParent $parent)
    {
        $name = $parent->full_name;

        if ($parent->user) {
            $parent->user->update(['is_active' => false]);
        }

        $parent->delete();

        AuditLogService::log('parent_deleted', "Parent {$name} removed", $parent);

        return redirect()->route('admin.parents.index')
            ->with('success', 'Parent removed successfully.');
    }
}
