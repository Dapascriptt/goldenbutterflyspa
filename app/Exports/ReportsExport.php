<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportsExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return collect(self::dummyRows());
    }

    public function headings(): array
    {
        return ['Tanggal', 'Kode', 'Keterangan', 'Jumlah'];
    }

    public static function dummyRows(): array
    {
        return [
            ['2026-02-01', 'RPT-001', 'Total Omset', 1250000],
            ['2026-02-01', 'RPT-002', 'Total Therapist', 12],
            ['2026-02-01', 'RPT-003', 'Total Inventory', 84],
        ];
    }
}
