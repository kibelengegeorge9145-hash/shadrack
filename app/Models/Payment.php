<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const METHODS = ['cash', 'bank', 'mobile', 'cheque', 'card', 'other'];

    protected $fillable = [
        'receipt_number', 'student_id', 'student_fee_id', 'fee_structure_id',
        'academic_year_id', 'term_id', 'recorded_by', 'amount', 'method',
        'reference', 'payment_date', 'notes', 'status',
    ];

    protected $casts = ['amount' => 'decimal:2', 'payment_date' => 'date'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function studentFee(): BelongsTo
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getMethodLabelAttribute(): string
    {
        return ucfirst((string) $this->method);
    }
}
