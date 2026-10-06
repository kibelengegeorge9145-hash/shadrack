<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::with('author')->latest();

        if ($request->get('status') === 'published') {
            $query->where('is_published', true);
        } elseif ($request->get('status') === 'draft') {
            $query->where('is_published', false);
        }

        $articles = $query->paginate(15)->withQueryString();

        return view('admin.news.index', compact('articles'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'image' => ['nullable', 'url', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['slug'] = Str::slug($data['title']);
        $existing = News::where('slug', $data['slug'])->count();
        if ($existing > 0) {
            $data['slug'] = $data['slug'].'-'.($existing + 1);
        }

        $data['author_id'] = auth()->id();
        $data['is_published'] = $request->boolean('is_published', false);
        $data['published_at'] = $data['published_at'] ?? ($data['is_published'] ? now() : null);

        $article = News::create($data);

        AuditLogService::log('news_created', "News article '{$article->title}' created", $article);

        return redirect()->route('admin.news.index')
            ->with('success', 'News article created successfully.');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'image' => ['nullable', 'url', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $slug = Str::slug($data['title']);
        $existing = News::where('slug', $slug)->where('id', '!=', $news->id)->count();
        if ($existing > 0) {
            $slug = $slug.'-'.($existing + 1);
        }
        $data['slug'] = $slug;

        $data['is_published'] = $request->boolean('is_published', false);
        if ($data['is_published'] && ! $news->is_published && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $news->update($data);

        AuditLogService::log('news_updated', "News article '{$news->title}' updated", $news);

        return redirect()->route('admin.news.index')
            ->with('success', 'News article updated successfully.');
    }

    public function destroy(News $news)
    {
        $title = $news->title;
        $news->delete();

        AuditLogService::log('news_deleted', "News article '{$title}' deleted");

        return redirect()->route('admin.news.index')
            ->with('success', 'News article deleted successfully.');
    }
}
