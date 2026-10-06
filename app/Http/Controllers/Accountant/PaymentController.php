<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Term;
use App\Services\FeeService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $year = AcademicYear::where('is_current', true)->first();
        $term = $year ? Term::where('is_current', true)->where('academic_year_id', $year->id)->first() : null;

        $query = Payment::with('student', 'feeStructure', 'recorder')
            ->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($s) use ($search) {
                        $s->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('admission_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($method = $request->get('method')) {
            $query->where('method', $method);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $payments = $query->paginate(15)->withQueryString();

        $students = Student::active()->orderBy('first_name')->get()->mapWithKeys(function ($s) {
            return [$s->id => $s->full_name.' ('.($s->admission_number ?? $s->id).')'];
        });

        $structures = FeeStructure::where('is_active', true)
            ->when($year?->id, fn ($q) => $q->where(fn ($s) => $s->whereNull('academic_year_id')->orWhere('academic_year_id', $year->id)))
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($f) => [$f->id => $f->name.' — TZS '.number_format((float) $f->amount, 2)]);

        $methods = collect(Payment::METHODS)->mapWithKeys(fn ($m) => [$m => ucfirst($m)]);
        $currentStudent = $request->get('student_id');

        return view('accountant.payments', compact(
            'payments',
            'students',
            'structures',
            'methods',
            'year',
            'term',
            'currentStudent',
        ));
    }

    public function store(Request $request, FeeService $feeService)
    {
        $year = AcademicYear::where('is_current', true)->first();
        $term = $year ? Term::where('is_current', true)->where('academic_year_id', $year->id)->first() : null;

        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'fee_structure_id' => ['required', 'exists:fee_structures,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'method' => ['required', 'in:'.implode(',', Payment::METHODS)],
            'payment_date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:191'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $studentFee = StudentFee::where('student_id', $data['student_id'])
            ->where('fee_structure_id', $data['fee_structure_id'])
            ->where('academic_year_id', $year?->id)
            ->where('term_id', $term?->id)
            ->first();

        $data['student_fee_id'] = $studentFee?->id;

        $payment = $feeService->recordPayment($data, $year?->id, $term?->id);

        return redirect()->route('accountant.payments.index', ['student_id' => $data['student_id']])
            ->with('success', 'Payment of TZS '.number_format((float) $payment->amount, 2)." recorded. Receipt: {$payment->receipt_number}");
    }
}
