<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($level = $request->get('level')) {
            $query->where('level', $level);
        }

        $subjects = $query->paginate(15)->withQueryString();

        $levelOptions = ['O-Level' => 'O-Level', 'A-Level' => 'A-Level'];

        return view('admin.subjects.index', compact('subjects', 'levelOptions'));
    }

    public function create()
    {
        $levelOptions = ['O-Level' => 'O-Level', 'A-Level' => 'A-Level'];

        return view('admin.subjects.create', compact('levelOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:subjects,code'],
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'in:O-Level,A-Level'],
            'department' => ['nullable', 'string', 'max:255'],
            'is_optional' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $subject = Subject::create([
            'code' => $data['code'],
            'name' => $data['name'],
            'level' => $data['level'],
            'department' => $data['department'] ?? null,
            'is_optional' => $request->boolean('is_optional', false),
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLogService::log('subject_created', "Subject {$data['name']} ({$data['code']}) created", $subject);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    public function edit(Subject $subject)
    {
        $levelOptions = ['O-Level' => 'O-Level', 'A-Level' => 'A-Level'];

        return view('admin.subjects.edit', compact('subject', 'levelOptions'));
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:subjects,code,'.$subject->id],
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'in:O-Level,A-Level'],
            'department' => ['nullable', 'string', 'max:255'],
            'is_optional' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $subject->update([
            'code' => $data['code'],
            'name' => $data['name'],
            'level' => $data['level'],
            'department' => $data['department'] ?? null,
            'is_optional' => $request->boolean('is_optional', false),
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLogService::log('subject_updated', "Subject {$data['name']} ({$data['code']}) updated", $subject);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        $name = $subject->name;

        $subject->delete();

        AuditLogService::log('subject_deleted', "Subject {$name} removed", $subject);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject removed successfully.');
    }
}
