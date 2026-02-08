<?php

namespace App\Exports;

use App\Models\Inventory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventoryExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected int $periodYear,
        protected int $periodMonth
    ) {
    }

    public function collection(): Collection
    {
        return Inventory::query()
            ->leftJoin('inventory_period_stocks as ps', function ($join) {
                $join->on('ps.inventory_id', '=', 'inventories.id')
                    ->where('ps.period_year', '=', $this->periodYear)
                    ->where('ps.period_month', '=', $this->periodMonth);
            })
            ->select([
                'inventories.id',
                'inventories.name',
                'inventories.stock_awal',
                'inventories.unit',
                'ps.stock_awal as period_stock_awal',
                'ps.stock_akhir as period_stock_akhir',
            ])
            ->orderBy('inventories.id')
            ->get()
            ->map(function ($row) {
                $stockAwal = $row->period_stock_awal ?? $row->stock_awal;
                $stockAkhir = $row->period_stock_akhir ?? $stockAwal;

                return [
                    $row->id,
                    $row->name,
                    $stockAwal,
                    $stockAkhir,
                    $row->unit ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return ['ID', 'Nama Item', 'Stok Awal', 'Stok Akhir', 'Satuan'];
    }
}
