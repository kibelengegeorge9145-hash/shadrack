<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscriber::latest('created_at');

        if ($request->get('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->get('status') === 'unsubscribed') {
            $query->where('is_active', false);
        }

        $subscribers = $query->paginate(15)->withQueryString();

        return view('admin.subscribers.index', compact('subscribers'));
    }

    public function destroy(Subscriber $subscriber)
    {
        $email = $subscriber->email;
        $subscriber->delete();

        AuditLogService::log('subscriber_deleted', "Subscriber '{$email}' removed");

        return redirect()->route('admin.subscribers.index')
            ->with('success', 'Subscriber removed successfully.');
    }
}
