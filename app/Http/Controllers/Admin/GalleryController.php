<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $photos = Gallery::latest()->paginate(20);

        return view('admin.gallery', compact('photos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'url', 'max:500'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
            'caption' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('gallery', 'public');
            $data['image'] = '/storage/'.$path;
        } elseif (! $request->filled('image')) {
            return back()->withErrors(['image' => 'Provide an image — upload a file or paste a URL.'])->withInput();
        }

        $photo = Gallery::create($data);

        AuditLogService::log('gallery_created', "Gallery image '{$photo->title}' added", $photo);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery image added successfully.');
    }

    public function destroy(Gallery $gallery)
    {
        $title = $gallery->title;
        $gallery->delete();

        AuditLogService::log('gallery_deleted', "Gallery image '{$title}' deleted");

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery image deleted successfully.');
    }
}
