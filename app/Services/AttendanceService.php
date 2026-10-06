<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Support\Collection;

class AttendanceService
{
    /**
     * Build/upsert attendance for a list of students on a given date.
     * Returns the count of records saved.
     */
    public function saveDaily(array $studentStatuses, int $schoolClassId, ?int $academicYearId, ?int $termId, string $date): int
    {
        $count = 0;

        foreach ($studentStatuses as $studentId => $status) {
            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'date' => $date],
                [
                    'school_class_id' => $schoolClassId,
                    'academic_year_id' => $academicYearId,
                    'term_id' => $termId,
                    'status' => $status,
                    'recorded_by' => auth()->id() ?? 1,
                ]
            );
            $count++;
        }

        return $count;
    }

    public function percentage(Collection $records, string $status = 'present'): float
    {
        $total = $records->count();
        if ($total == 0) {
            return 0;
        }

        return round(($records->where('status', $status)->count() / $total) * 100, 1);
    }

    public function monthlySummary(int $studentId, ?int $academicYearId = null): array
    {
        $base = Attendance::where('student_id', $studentId)
            ->when($academicYearId, fn ($q) => $q->where('academic_year_id', $academicYearId))
            ->get();

        return [
            'present' => $base->where('status', 'present')->count(),
            'absent' => $base->where('status', 'absent')->count(),
            'late' => $base->where('status', 'late')->count(),
            'excused' => $base->where('status', 'excused')->count(),
            'percentage' => $this->percentage($base, 'present'),
        ];
    }
}
