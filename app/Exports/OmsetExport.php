<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OmsetExport implements FromCollection, WithHeadings
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
            ['2026-02-01', 'OMS-001', 'Omset harian', 250000],
            ['2026-02-02', 'OMS-002', 'Omset harian', 320000],
            ['2026-02-03', 'OMS-003', 'Omset harian', 180000],
        ];
    }
}
