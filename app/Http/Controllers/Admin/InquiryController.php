<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inquiry::latest();

        if ($request->get('status') === 'unread') {
            $query->unread();
        } elseif ($request->get('status') === 'read') {
            $query->where('is_read', true);
        }

        $inquiries = $query->paginate(15)->withQueryString();

        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function show(Inquiry $inquiry)
    {
        if (! $inquiry->is_read) {
            $inquiry->update(['is_read' => true]);
            AuditLogService::log('inquiry_marked_read', "Inquiry #{$inquiry->id} marked as read", $inquiry);
        }

        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function destroy(Inquiry $inquiry)
    {
        $id = $inquiry->id;
        $inquiry->delete();

        AuditLogService::log('inquiry_deleted', "Inquiry #{$id} deleted");

        return redirect()->route('admin.inquiries.index')
            ->with('success', 'Inquiry deleted successfully.');
    }
}
