<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Arrayable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Dumb data-driven worksheet used by every Academic Report Center export.
 */
class GenericReportExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        protected array $headings,
        protected array|Arrayable $rows,
        protected string $title = 'Report',
    ) {}

    public function headings(): array
    {
        return $this->headings;
    }

    public function array(): array
    {
        return $this->rows instanceof Arrayable ? $this->rows->toArray() : $this->rows;
    }

    public function title(): string
    {
        return mb_substr($this->title, 0, 31);
    }

    public function styles(Worksheet $sheet): ?array
    {
        $range = 'A1:'.$sheet->getHighestColumn().'1';

        $sheet->getStyle($range)
            ->getFont()
            ->setBold(true)
            ->setColor(new Color('FFFFFF'));

        $sheet->getStyle($range)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FF0F1E3D');

        return [];
    }
}
