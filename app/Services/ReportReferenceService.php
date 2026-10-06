<?php

namespace App\Services;

use App\Models\ReportSequence;
use Illuminate\Support\Facades\DB;

/**
 * Issues unique, non-reused EBSS-RPT-{year}-{sequence} report references.
 *
 * A single monotonic counter exists per calendar year and is incremented
 * inside a row-locked transaction so simultaneous report generations never
 * hand out the same reference.
 */
class ReportReferenceService
{
    public function next(?int $year = null): string
    {
        $year = $year ?? now()->year;

        return DB::transaction(function () use ($year): string {
            $seq = ReportSequence::where('year', $year)->lockForUpdate()->first();

            if (! $seq) {
                $seq = ReportSequence::create(['year' => $year, 'sequence' => 1]);
            } else {
                $seq->increment('sequence');
                $seq->refresh();
            }

            return 'EBSS-RPT-'.$year.'-'.str_pad((string) $seq->sequence, 5, '0', STR_PAD_LEFT);
        });
    }
}
