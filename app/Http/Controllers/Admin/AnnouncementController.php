<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\SchoolClass;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::with(['author', 'schoolClass'])->latest();

        if ($audience = $request->get('audience')) {
            $query->where('audience', $audience);
        }

        $announcements = $query->paginate(15)->withQueryString();
        $audiences = array_flip(Announcement::AUDIENCES);

        return view('admin.announcements.index', compact('announcements', 'audiences'));
    }

    public function create()
    {
        $audiences = array_flip(Announcement::AUDIENCES);
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('admin.announcements.create', compact('audiences', 'classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'audience' => ['required', 'in:'.implode(',', Announcement::AUDIENCES)],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['author_id'] = auth()->id();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['published_at'] = now();

        $announcement = Announcement::create($data);

        AuditLogService::log('announcement_created', "Announcement '{$announcement->title}' created", $announcement);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement)
    {
        $audiences = array_flip(Announcement::AUDIENCES);
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('admin.announcements.edit', compact('announcement', 'audiences', 'classes'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'audience' => ['required', 'in:'.implode(',', Announcement::AUDIENCES)],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $announcement->update($data);

        AuditLogService::log('announcement_updated', "Announcement '{$announcement->title}' updated", $announcement);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $title = $announcement->title;
        $announcement->delete();

        AuditLogService::log('announcement_deleted', "Announcement '{$title}' deleted");

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
}
