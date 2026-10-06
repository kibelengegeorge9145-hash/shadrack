<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Admission::latest();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('parent_name', 'like', "%{$search}%");
            });
        }

        $admissions = $query->paginate(15)->withQueryString();
        $pendingCount = Admission::where('status', 'pending')->count();
        $statuses = ['pending' => 'Pending', 'under_review' => 'Under Review', 'accepted' => 'Accepted', 'rejected' => 'Rejected'];

        return view('admin.admissions.index', compact('admissions', 'pendingCount', 'statuses'));
    }

    public function create()
    {
        $statuses = Admission::STATUSES;

        return view('admin.admissions.create', compact('statuses'));
    }

    public function store(Request $request)
    {
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

        $admission = Admission::create($data);

        AuditLogService::log('admission_created', "Admission application {$applicationNumber} created", $admission);

        return redirect()->route('admin.admissions.show', $admission)
            ->with('success', "Admission {$applicationNumber} created successfully.");
    }

    public function show(Admission $admission)
    {
        // Legacy inline POST handling — kept so any existing view that
        // still posts to admin.admissions.show keeps working.
        if (request()->isMethod('post')) {
            $data = request()->validate([
                'status' => ['required', 'in:accepted,rejected,under_review'],
            ]);

            $admission->update($data);

            AuditLogService::log('admission_status_changed', "Admission {$admission->application_number} status changed to {$data['status']}", $admission);

            return back()->with('success', 'Admission status updated successfully.');
        }

        return view('admin.admissions.show', compact('admission'));
    }

    /**
     * Update only the status of an admission application.
     * Routed via PATCH /admin/admissions/{admission}/status
     */
    public function updateStatus(Request $request, Admission $admission)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,under_review,accepted,rejected'],
        ]);

        $admission->update($data);

        AuditLogService::log(
            'admission_status_changed',
            "Admission {$admission->application_number} status changed to {$data['status']}",
            $admission
        );

        return back()->with('success', 'Admission status updated successfully.');
    }

    public function destroy(Admission $admission)
    {
        $number = $admission->application_number;
        $admission->delete();

        AuditLogService::log('admission_deleted', "Admission {$number} deleted");

        return redirect()->route('admin.admissions.index')
            ->with('success', 'Admission deleted successfully.');
    }
}