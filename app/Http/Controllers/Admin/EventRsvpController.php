<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Services\AuditLogService;

class EventRsvpController extends Controller
{
    public function index(Event $event)
    {
        $rsvps = $event->rsvps()->latest()->paginate(15);

        return view('admin.events.rsvps', compact('event', 'rsvps'));
    }

    public function destroy(Event $event, EventRsvp $rsvp)
    {
        $email = $rsvp->email;
        $rsvp->delete();

        AuditLogService::log('rsvp_deleted', "RSVP removed for '{$email}' on event '{$event->title}'");

        return back()->with('success', 'RSVP removed successfully.');
    }
}
