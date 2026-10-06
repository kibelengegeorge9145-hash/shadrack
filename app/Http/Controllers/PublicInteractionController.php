<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\Inquiry;
use App\Models\News;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicInteractionController extends Controller
{
    /**
     * Spam honeypot: a hidden field that must stay empty.
     */
    protected function honeypotValid(Request $request): bool
    {
        return trim((string) $request->input('website')) === '';
    }

    protected function ip(Request $request): ?string
    {
        return $request->ip();
    }

    public function contact(Request $request)
    {
        if (! $this->honeypotValid($request)) {
            return back()->with('success', 'Your message has been sent successfully.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        Inquiry::create($data + ['ip_address' => $this->ip($request)]);

        return back()->with('success', 'Thank you! Your message has been sent successfully. We will respond shortly.');
    }

    public function subscribe(Request $request)
    {
        if (! $this->honeypotValid($request)) {
            return back()->with('success', 'You have been subscribed successfully.');
        }

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $subscriber = Subscriber::where('email', $data['email'])->first();

        if ($subscriber) {
            $subscriber->update(['is_active' => true, 'unsubscribed_at' => null]);
        } else {
            Subscriber::create($data + ['ip_address' => $this->ip($request)]);
        }

        return back()->with('success', 'Thank you for subscribing to our updates.');
    }

    public function unsubscribe(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        Subscriber::where('email', $data['email'])
            ->update(['is_active' => false, 'unsubscribed_at' => now()]);

        return back()->with('success', 'You have been unsubscribed successfully.');
    }

    /* -----------------------------------------------------------------
     |  Admissions
     | ----------------------------------------------------------------- */

    /**
     * Handle a new public admission application submission.
     */
    public function admission(Request $request)
    {
        // Honeypot: silently accept bots so they don't retry
        if (! $this->honeypotValid($request)) {
            return back()->with('success', 'Your application has been received. We will contact you shortly.');
        }

        $data = $request->validate([
            'first_name'     => ['required', 'string', 'max:100'],
            'middle_name'    => ['nullable', 'string', 'max:100'],
            'last_name'      => ['required', 'string', 'max:100'],
            'date_of_birth'  => ['required', 'date', 'before:today'],
            'gender'         => ['required', 'in:male,female'],
            'level'          => ['required', 'string', 'max:50'],
            'applying_class' => ['required', 'string', 'max:50'],
            'current_school' => ['nullable', 'string', 'max:150'],
            'parent_name'    => ['required', 'string', 'max:150'],
            'parent_phone'   => ['required', 'string', 'max:20'],
            'parent_email'   => ['required', 'email', 'max:150'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ]);

        $admission = DB::transaction(function () use ($data, $request) {
            $data['application_number'] = $this->generateApplicationNumber();
            $data['status']             = 'pending';
            $data['ip_address']         = $this->ip($request);

            return Admission::create($data);
        });

        return redirect()
            ->route('admissions')
            ->with('success', 'Your application has been submitted successfully. '
                . 'Your application number is ' . $admission->application_number
                . '. Please keep it safe — you can use it to check your application status.');
    }

    /**
     * Generate a sequential application number like ADM-2026-0001.
     * Retries on the off chance of a collision.
     */
    protected function generateApplicationNumber(): string
    {
        $year = now()->year;

        do {
            $last = Admission::whereYear('created_at', $year)->lockForUpdate()->count();
            $number = sprintf('ADM-%d-%04d', $year, $last + 1);
        } while (Admission::where('application_number', $number)->exists());

        return $number;
    }

    public function admissionStatusForm()
    {
        return view('public.admission-status');
    }

    public function admissionStatus(Request $request)
    {
        if (! $this->honeypotValid($request)) {
            return back()->withErrors(['application_number' => 'Invalid request.']);
        }

        $data = $request->validate([
            'application_number' => ['required', 'string', 'max:30', 'exists:admissions,application_number'],
            'parent_email' => ['nullable', 'email', 'max:255'],
            'parent_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $email = $request->input('parent_email');
        $phone = $request->input('parent_phone');

        if (blank($email) && blank($phone)) {
            return back()->withErrors(['parent_email' => 'Provide the email or phone used in the application.']);
        }

        $admission = Admission::where('application_number', $data['application_number'])
            ->where(function ($q) use ($email, $phone) {
                $q->where(fn ($qq) => filled($email) ? $qq->where('parent_email', $email) : $qq->whereNull('id'))
                    ->orWhere(fn ($qq) => filled($phone) ? $qq->where('parent_phone', $phone) : $qq->whereNull('id'));
            })
            ->first();

        if (! $admission) {
            return back()->withErrors(['application_number' => 'No application matches the details provided.']);
        }

        return view('public.admission-status', ['checked' => $admission]);
    }

    /* -----------------------------------------------------------------
     |  Events / News
     | ----------------------------------------------------------------- */

    public function rsvp(Event $event, Request $request)
    {
        if (! $event->is_published) {
            abort(404);
        }

        if (! $this->honeypotValid($request)) {
            return back()->with('success', 'Your RSVP has been recorded.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'guests' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $previous = EventRsvp::where('event_id', $event->id)
            ->where('email', $data['email'])
            ->first();

        if ($previous) {
            return back()->with('info', 'This email is already registered for the event.');
        }

        $data['guests'] = (int) ($data['guests'] ?? 1);
        $event->rsvps()->create($data + ['ip_address' => $this->ip($request)]);

        return back()->with('success', 'Your RSVP has been recorded. We look forward to seeing you!');
    }

    public function comment(News $news, Request $request)
    {
        if (! $news->is_published) {
            abort(404);
        }

        if (! $this->honeypotValid($request)) {
            return back()->with('success', 'Your comment has been submitted for review.');
        }

        $data = $request->validate([
            'author_name' => ['required', 'string', 'max:120'],
            'author_email' => ['nullable', 'email', 'max:120'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $news->comments()->create($data + [
            'user_id' => auth()->id(),
            'is_approved' => false,
            'ip_address' => $this->ip($request),
        ]);

        return back()->with('success', 'Your comment has been submitted and will appear after moderation.');
    }
}