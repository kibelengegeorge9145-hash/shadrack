<?php

namespace App\Services;

use App\Models\FeeStructure;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentFee;

class FeeService
{
    /**
     * Build/refresh a student's fee ledger from active fee structures.
     * Assigns the required amount for each structure and updates balances.
     */
    public function syncStudentFees(Student $student, ?int $academicYearId = null, ?int $termId = null): void
    {
        // Fee structures are not term-scoped; term scoping happens on student_fees.
        $structures = FeeStructure::where('is_active', true)
            ->when($academicYearId, fn ($q) => $q->where(fn ($s) => $s->whereNull('academic_year_id')->orWhere('academic_year_id', $academicYearId)))
            ->get();

        foreach ($structures as $structure) {
            $applies = $this->structureApplies($structure, $student);
            if (! $applies) {
                continue;
            }

            $studentFee = StudentFee::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'fee_structure_id' => $structure->id,
                    'academic_year_id' => $academicYearId ?? $structure->academic_year_id,
                    'term_id' => $termId,
                ],
                ['required_amount' => $structure->amount, 'paid_amount' => 0, 'balance' => $structure->amount]
            );

            // Only update required amount if not paid to avoid erasing history changes.
            if ((float) $studentFee->paid_amount == 0) {
                $studentFee->required_amount = $structure->amount;
            }

            $studentFee->paid_amount = $this->paidForStructure($student->id, $structure->id, $academicYearId, $termId);
            $studentFee->balance = max(0, $studentFee->required_amount - $studentFee->paid_amount);
            $studentFee->save();
        }
    }

    protected function structureApplies(FeeStructure $structure, Student $student): bool
    {
        if ($structure->school_class_id && $structure->school_class_id != $student->school_class_id) {
            return false;
        }
        if ($structure->level && $structure->level != $student->level) {
            return false;
        }

        return true;
    }

    protected function paidForStructure(int $studentId, int $feeStructureId, ?int $academicYearId, ?int $termId): float
    {
        return (float) Payment::where('student_id', $studentId)
            ->where('fee_structure_id', $feeStructureId)
            ->where('status', 'recorded')
            ->when($academicYearId, fn ($q) => $q->where('academic_year_id', $academicYearId))
            ->when($termId, fn ($q) => $q->where('term_id', $termId))
            ->sum('amount');
    }

    /**
     * Record a payment, update balances, and generate a receipt + audit log.
     */
    public function recordPayment(array $data, ?int $academicYearId = null, ?int $termId = null): Payment
    {
        $student = Student::findOrFail($data['student_id']);
        $amount = (float) $data['amount'];

        $payment = Payment::create([
            'receipt_number' => $this->nextReceiptNumber(),
            'student_id' => $student->id,
            'student_fee_id' => $data['student_fee_id'] ?? null,
            'fee_structure_id' => $data['fee_structure_id'] ?? null,
            'academic_year_id' => $academicYearId ?? ($data['academic_year_id'] ?? null),
            'term_id' => $termId ?? ($data['term_id'] ?? null),
            'recorded_by' => auth()->id(),
            'amount' => $amount,
            'method' => $data['method'],
            'reference' => $data['reference'] ?? null,
            'payment_date' => $data['payment_date'] ?? now(),
            'notes' => $data['notes'] ?? null,
            'status' => 'recorded',
        ]);

        // Recalculate the student fee ledger from this payment.
        $this->recalculateLedger($student, $payment->fee_structure_id, $payment->academic_year_id, $payment->term_id);

        AuditLogService::log(
            'payment_recorded',
            "Payment of {$payment->amount} recorded for {$student->full_name}",
            $payment,
            ['receipt' => $payment->receipt_number]
        );

        return $payment;
    }

    /**
     * Reverse (cancel) a payment and restore balances.
     */
    public function reversePayment(Payment $payment): void
    {
        $payment->status = 'reversed';
        $payment->save();

        $this->recalculateLedger($payment->student, $payment->fee_structure_id, $payment->academic_year_id, $payment->term_id);

        AuditLogService::log('payment_reversed', "Payment {$payment->receipt_number} reversed", $payment);
    }

    protected function recalculateLedger(Student $student, ?int $feeStructureId, ?int $academicYearId, ?int $termId): void
    {
        $query = StudentFee::where('student_id', $student->id)
            ->when($feeStructureId, fn ($q) => $q->where('fee_structure_id', $feeStructureId))
            ->when($academicYearId, fn ($q) => $q->where('academic_year_id', $academicYearId))
            ->when($termId, fn ($q) => $q->where('term_id', $termId));

        foreach ($query->get() as $studentFee) {
            $paid = Payment::where('student_id', $student->id)
                ->where('fee_structure_id', $studentFee->fee_structure_id)
                ->where('status', 'recorded')
                ->when($studentFee->academic_year_id, fn ($q) => $q->where('academic_year_id', $studentFee->academic_year_id))
                ->when($studentFee->term_id, fn ($q) => $q->where('term_id', $studentFee->term_id))
                ->sum('amount');

            $studentFee->paid_amount = (float) $paid;
            $studentFee->balance = max(0, (float) $studentFee->required_amount - (float) $paid);
            $studentFee->save();
        }
    }

    public function nextReceiptNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'RCP-'.$year.'-';
        $last = Payment::orderBy('id', 'desc')->value('receipt_number');
        $sequence = 1;

        if ($last && str_starts_with($last, $prefix)) {
            $sequence = ((int) substr($last, strlen($prefix))) + 1;
        }

        return $prefix.str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
    }
}
