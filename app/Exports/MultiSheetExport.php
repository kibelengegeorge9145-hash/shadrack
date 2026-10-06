<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetExport implements Export, WithMultipleSheets
{
    public function __construct(protected array $sheets) {}

    public function sheets(): array
    {
        return $this->sheets;
    }
}
