<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\News;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $latestNews = News::where('is_published', true)
            ->latest('published_at')
            ->limit(3)
            ->get();

        $upcomingEvents = Event::where('is_published', true)
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->limit(3)
            ->get();

        $recentPhotos = Gallery::latest()->limit(8)->get();

        return view('public.home', compact('latestNews', 'upcomingEvents', 'recentPhotos'));
    }

    public function about()
    {
        return view('public.about');
    }

    public function academics()
    {
        return view('public.academics');
    }

    public function olevel()
    {
        return view('public.olevel');
    }

    public function alevel()
    {
        return view('public.alevel');
    }

    public function admissions(Request $request)
    {
        if ($request->isMethod('post')) {
            if (trim((string) $request->input('website')) !== '') {
                return back()->with('success', 'Application submitted successfully!');
            }

            $data = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'middle_name' => ['nullable', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'date_of_birth' => ['required', 'date', 'before:today'],
                'gender' => ['required', 'in:male,female'],
                'current_school' => ['nullable', 'string', 'max:255'],
                'applying_class' => ['required', 'string', 'max:255'],
                'level' => ['required', 'in:O-Level,A-Level'],
                'parent_name' => ['required', 'string', 'max:255'],
                'parent_phone' => ['required', 'string', 'max:20'],
                'parent_email' => ['nullable', 'email', 'max:255'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ]);

            $year = now()->format('Y');
            $maxNum = Admission::where('application_number', 'like', "ADM-{$year}-%")
                ->max('application_number');
            $sequence = 1;
            if ($maxNum) {
                $sequence = ((int) substr($maxNum, strlen("ADM-{$year}-"))) + 1;
            }
            $applicationNumber = 'ADM-'.$year.'-'.str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);

            $data['application_number'] = $applicationNumber;
            $data['status'] = 'pending';

            Admission::create($data);

            return back()->with('success', "Application submitted successfully! Your application number is {$applicationNumber}.");
        }

        return view('public.admissions');
    }

    public function news()
    {
        $news = News::where('is_published', true)
            ->latest('published_at')
            ->paginate(9);

        return view('public.news', compact('news'));
    }

    public function newsShow(News $news)
    {
        if (! $news->is_published) {
            abort(404);
        }

        $comments = $news->comments()
            ->with('user')
            ->latest()
            ->get();

        $related = News::where('is_published', true)
            ->whereKeyNot($news->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('public.news-show', compact('news', 'comments', 'related'));
    }

    public function announcements()
    {
        $announcements = Announcement::where('is_active', true)
            ->where(function ($q) {
                $q->whereIn('audience', ['everyone'])
                    ->orWhere('audience', 'students')
                    ->orWhere('audience', 'parents');
            })
            ->latest('published_at')
            ->paginate(12);

        return view('public.announcements', compact('announcements'));
    }

    public function events()
    {
        $events = Event::where('is_published', true)
            ->orderBy('starts_at')
            ->paginate(9);

        return view('public.events', compact('events'));
    }

    public function gallery()
    {
        $photos = Gallery::latest()->paginate(12);

        return view('public.gallery', compact('photos'));
    }

    public function contact()
    {
        return view('public.contact');
    }
}
