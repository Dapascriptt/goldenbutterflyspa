<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TherapistExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return collect(self::dummyRows());
    }

    public function headings(): array
    {
        return ['ID', 'Nama', 'Spesialisasi', 'Status'];
    }

    public static function dummyRows(): array
    {
        return [
            [1, 'Therapist A', 'Massage', 'Aktif'],
            [2, 'Therapist B', 'Reflexology', 'Aktif'],
            [3, 'Therapist C', 'Spa', 'Nonaktif'],
        ];
    }
}
