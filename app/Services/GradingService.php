<?php

namespace App\Services;

use App\Models\DivisionRule;
use App\Models\GradingRule;
use App\Models\Result;
use App\Models\Student;

class GradingService
{
    /**
     * Resolve grade information for a numeric mark at a given level.
     */
    public function grade(int $mark, string $level): array
    {
        $rules = GradingRule::where('level', $level)->orderBy('min_mark')->get();

        foreach ($rules as $rule) {
            if ($mark >= $rule->min_mark && $mark <= $rule->max_mark) {
                return [
                    'grade' => $rule->grade,
                    'points' => $rule->points,
                    'is_pass' => (bool) $rule->is_pass,
                ];
            }
        }

        return ['grade' => 'F', 'points' => 0, 'is_pass' => false];
    }

    /**
     * Determine the division from total points, passes and subjects taken.
     */
    public function division(int $totalPoints, int $passed, int $failed, int $subjectCount, string $level): ?string
    {
        if ($subjectCount <= 0) {
            return null;
        }

        // If the student fails every subject, they fall to Division 0.
        if ($passed <= 0 && $failed > 0) {
            return 'Division 0';
        }

        $rules = DivisionRule::where('level', $level)->orderBy('sort_order')->get();

        foreach ($rules as $rule) {
            if ($totalPoints >= $rule->min_points && $totalPoints <= $rule->max_points) {
                return $rule->division;
            }
        }

        return 'Division 0';
    }

    /**
     * Recompute the summary totals for a result based on its items.
     *
     * Returns the updated attributes array (does not persist).
     */
    public function summarize(Result $result): array
    {
        $totalMarks = 0;
        $totalPoints = 0;
        $passed = 0;
        $failed = 0;
        $count = 0;

        foreach ($result->items as $item) {
            $count++;
            $totalMarks += (int) $item->marks;
            $totalPoints += (int) $item->points;
            $isPass = $this->isItemPassing($item->grade, $result->level);
            if ($isPass) {
                $passed++;
            } else {
                $failed++;
            }
        }

        $average = $count > 0 ? round($totalMarks / $count, 2) : 0;

        return [
            'total_marks' => $totalMarks,
            'average' => $average,
            'total_points' => $totalPoints,
            'passed_subjects' => $passed,
            'failed_subjects' => $failed,
            'division' => $this->division($totalPoints, $passed, $failed, $count, $result->level),
        ];
    }

    /**
     * Determine if a grade string represents a pass at a given level.
     */
    protected function isItemPassing(?string $grade, string $level): bool
    {
        if (! $grade) {
            return false;
        }

        $rule = GradingRule::where('level', $level)->where('grade', $grade)->first();

        return $rule ? (bool) $rule->is_pass : $grade !== 'F';
    }

    /**
     * Compute and assign positions across results of the same class/term/year/level.
     */
    public function assignPositions($results): void
    {
        $ranked = $results->sortByDesc('total_points')->sortByDesc('average')->values();

        $previousPoints = null;
        $previousAverage = null;
        $rank = 0;
        $tie = 1;

        foreach ($ranked as $index => $result) {
            if ($index === 0 || $result->total_points !== $previousPoints || round($result->average, 2) !== $previousAverage) {
                $rank = $index + 1;
                $tie = 1;
            } else {
                $tie++;
            }

            $ordinal = $this->ordinal($rank);
            $result->position = $ordinal;
            $result->save();

            $previousPoints = $result->total_points;
            $previousAverage = round($result->average, 2);
        }
    }

    protected function ordinal(int $number): string
    {
        static $suffixes = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            $suffix = 'th';
        } else {
            $suffix = $suffixes[$number % 10] ?? 'th';
        }

        return $number.$suffix;
    }

    /**
     * Convenience: load the latest published result for a student through a term/year.
     */
    public function latestResult(Student $student): ?Result
    {
        return $student->results()
            ->where('status', Result::STATUS_PUBLISHED)
            ->latest('published_at')
            ->with(['items.subject'])
            ->first();
    }
}
