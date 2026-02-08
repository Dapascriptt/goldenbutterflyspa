<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Exports\InventoryExport;
use App\Http\Requests\InventoryMovementUpdateRequest;
use App\Http\Requests\InventoryStockRequest;
use App\Http\Requests\InventoryStoreRequest;
use App\Http\Requests\InventoryUpdateRequest;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\InventoryPeriodStock;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InventoryController extends Controller
{
    public function index()
    {
        $period = $this->resolvePeriod(request());

        $search = trim((string) request('search', ''));

        $periodStockIds = InventoryPeriodStock::query()
            ->select('inventory_id')
            ->where('period_year', $period['year'])
            ->where('period_month', $period['month']);

        $items = Inventory::query()
            ->select(['id', 'name', 'unit', 'stock_awal'])
            ->whereIn('id', $periodStockIds)
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->orderBy('id')
            ->paginate(25)
            ->appends(['search' => $search] + $period['query']);

        $periodStocks = InventoryPeriodStock::query()
            ->whereIn('inventory_id', $items->pluck('id'))
            ->where('period_year', $period['year'])
            ->where('period_month', $period['month'])
            ->get()
            ->keyBy('inventory_id');

        $movementsByItem = InventoryMovement::query()
            ->select(['id', 'inventory_id', 'type', 'qty', 'note', 'movement_date'])
            ->whereIn('inventory_id', $items->pluck('id'))
            ->where('period_year', $period['year'])
            ->where('period_month', $period['month'])
            ->orderByDesc('movement_date')
            ->orderByDesc('id')
            ->get()
            ->groupBy('inventory_id');

        return view('manage.inventory.index', [
            'items' => $items,
            'period' => $period,
            'months' => $this->monthOptions(),
            'periodStocks' => $periodStocks,
            'search' => $search,
            'movementsByItem' => $movementsByItem,
        ]);
    }

    public function create()
    {
        return view('manage.inventory.create');
    }

    public function store(InventoryStoreRequest $request)
    {
        $data = $request->validated();

        $data['stock_akhir'] = $data['stock_awal'];

        $item = Inventory::create([
            'name' => $data['name'],
            'unit' => $data['unit'] ?? null,
            'stock_awal' => $data['stock_awal'],
            'stock_akhir' => $data['stock_awal'],
        ]);

        InventoryPeriodStock::create([
            'inventory_id' => $item->id,
            'period_year' => (int) $data['year'],
            'period_month' => (int) $data['month'],
            'stock_awal' => $data['stock_awal'],
            'stock_akhir' => $data['stock_awal'],
        ]);

        return redirect()
            ->route('manage.inventory.index', [
                'year' => (int) $data['year'],
                'month' => (int) $data['month'],
            ])
            ->with('status', 'Inventory tersimpan.');
    }

    public function edit(int $id)
    {
        $item = Inventory::findOrFail($id);

        $period = $this->resolvePeriod(request());
        $periodStock = $this->getPeriodStock($item, $period);

        $movements = InventoryMovement::query()
            ->where('inventory_id', $item->id)
            ->where('period_year', $period['year'])
            ->where('period_month', $period['month'])
            ->when($period['start_date'], fn ($q) => $q->whereDate('movement_date', '>=', $period['start_date']))
            ->when($period['end_date'], fn ($q) => $q->whereDate('movement_date', '<=', $period['end_date']))
            ->latest()
            ->paginate(10, ['*'], 'movements');

        return view('manage.inventory.edit', [
            'item' => $item,
            'movements' => $movements,
            'period' => $period,
            'periodStock' => $periodStock,
            'months' => $this->monthOptions(),
        ]);
    }

    public function update(InventoryUpdateRequest $request, int $id)
    {
        $item = Inventory::findOrFail($id);

        $data = $request->validated();

        $period = [
            'year' => (int) $data['year'],
            'month' => (int) $data['month'],
        ];
        $periodStock = $this->firstOrCreatePeriodStock($item, $period);

        $delta = $data['stock_awal'] - $periodStock->stock_awal;
        $newStockAkhir = $periodStock->stock_akhir + $delta;

        if ($newStockAkhir < 0) {
            return back()
                ->withErrors(['stock_awal' => 'Stok akhir tidak boleh negatif.'])
                ->withInput();
        }

        $item->update([
            'name' => $data['name'],
            'unit' => $data['unit'] ?? null,
        ]);

        $periodStock->update([
            'stock_awal' => $data['stock_awal'],
            'stock_akhir' => $newStockAkhir,
        ]);

        return redirect()
            ->route('manage.inventory.index', [
                'year' => $period['year'],
                'month' => $period['month'],
            ])
            ->with('status', 'Inventory diperbarui.');
    }

    public function destroy(int $id)
    {
        $item = Inventory::findOrFail($id);
        $item->delete();

        return redirect()
            ->route('manage.inventory.index')
            ->with('status', 'Inventory dihapus.');
    }

    public function stockInForm(int $id)
    {
        $item = Inventory::findOrFail($id);
        $period = $this->resolvePeriod(request());

        return view('manage.inventory.stock-in', [
            'item' => $item,
            'period' => $period,
        ]);
    }

    public function stockOutForm(int $id)
    {
        $item = Inventory::findOrFail($id);
        $period = $this->resolvePeriod(request());

        return view('manage.inventory.stock-out', [
            'item' => $item,
            'period' => $period,
        ]);
    }

    public function stockIn(InventoryStockRequest $request, int $id)
    {
        $item = Inventory::findOrFail($id);
        $period = $this->resolvePeriod($request);

        $data = $request->validated();

        if (!$this->dateMatchesPeriod($data['movement_date'], $period)) {
            return back()
                ->withErrors(['movement_date' => 'Tanggal harus berada pada periode bulan yang dipilih.'])
                ->withInput();
        }

        $periodStock = $this->firstOrCreatePeriodStock($item, $period);
        $periodStock->update([
            'stock_akhir' => $periodStock->stock_akhir + $data['qty'],
        ]);

        InventoryMovement::create([
            'inventory_id' => $item->id,
            'type' => 'in',
            'qty' => $data['qty'],
            'note' => $data['note'] ?? null,
            'movement_date' => $data['movement_date'],
            'period_year' => $period['year'],
            'period_month' => $period['month'],
        ]);

        return redirect()
            ->route('manage.inventory.index', $period['query'])
            ->with('status', 'Stock in berhasil.');
    }

    public function stockOut(InventoryStockRequest $request, int $id)
    {
        $item = Inventory::findOrFail($id);
        $period = $this->resolvePeriod($request);

        $data = $request->validated();

        if (!$this->dateMatchesPeriod($data['movement_date'], $period)) {
            return back()
                ->withErrors(['movement_date' => 'Tanggal harus berada pada periode bulan yang dipilih.'])
                ->withInput();
        }

        $periodStock = $this->firstOrCreatePeriodStock($item, $period);

        if ($data['qty'] > $periodStock->stock_akhir) {
            return back()
                ->withErrors(['qty' => 'Stok tidak mencukupi.'])
                ->withInput();
        }

        $periodStock->update([
            'stock_akhir' => $periodStock->stock_akhir - $data['qty'],
        ]);

        InventoryMovement::create([
            'inventory_id' => $item->id,
            'type' => 'out',
            'qty' => $data['qty'],
            'note' => $data['note'] ?? null,
            'movement_date' => $data['movement_date'],
            'period_year' => $period['year'],
            'period_month' => $period['month'],
        ]);

        return redirect()
            ->route('manage.inventory.index', $period['query'])
            ->with('status', 'Stock out berhasil.');
    }

    public function editMovement(int $id, int $movementId)
    {
        $item = Inventory::findOrFail($id);
        $movement = InventoryMovement::query()
            ->where('inventory_id', $item->id)
            ->findOrFail($movementId);

        return view('manage.inventory.movement-edit', [
            'item' => $item,
            'movement' => $movement,
            'period' => [
                'year' => $movement->period_year,
                'month' => $movement->period_month,
                'query' => [
                    'year' => $movement->period_year,
                    'month' => $movement->period_month,
                ],
            ],
        ]);
    }

    public function updateMovement(InventoryMovementUpdateRequest $request, int $id, int $movementId)
    {
        $item = Inventory::findOrFail($id);
        $movement = InventoryMovement::query()
            ->where('inventory_id', $item->id)
            ->findOrFail($movementId);

        $data = $request->validated();

        if (!$this->dateMatchesPeriod($data['movement_date'], [
            'year' => $movement->period_year,
            'month' => $movement->period_month,
        ])) {
            return back()
                ->withErrors(['movement_date' => 'Tanggal harus berada pada periode bulan yang sama.'])
                ->withInput();
        }

        $periodStock = $this->firstOrCreatePeriodStock($item, [
            'year' => $movement->period_year,
            'month' => $movement->period_month,
        ]);

        $oldQty = $movement->qty;
        $newQty = $data['qty'];
        $delta = $newQty - $oldQty;

        $newStockAkhir = $periodStock->stock_akhir;
        if ($movement->type === 'in') {
            $newStockAkhir += $delta;
        } else {
            $newStockAkhir -= $delta;
        }

        if ($newStockAkhir < 0) {
            return back()
                ->withErrors(['qty' => 'Stok akhir tidak boleh negatif.'])
                ->withInput();
        }

        $movement->update([
            'qty' => $newQty,
            'note' => $data['note'] ?? null,
            'movement_date' => $data['movement_date'],
        ]);

        $periodStock->update([
            'stock_akhir' => $newStockAkhir,
        ]);

        return redirect()
            ->route('manage.inventory.edit', array_merge(['id' => $item->id], [
                'year' => $movement->period_year,
                'month' => $movement->period_month,
            ]))
            ->with('status', 'Pergerakan stok diperbarui.');
    }

    public function exportExcel()
    {
        $period = $this->resolvePeriod(request());

        return Excel::download(new InventoryExport($period['year'], $period['month']), 'inventory.xlsx');
    }

    public function exportPdf()
    {
        $period = $this->resolvePeriod(request());

        $pdf = Pdf::loadView('manage.inventory.print', [
            'title' => 'Laporan Inventory',
            'period' => $period,
            'rows' => $this->inventoryRowsForPeriod($period),
        ])->setPaper('a4');

        return $pdf->download('inventory.pdf');
    }

    public function print()
    {
        $period = $this->resolvePeriod(request());

        return view('manage.inventory.print', [
            'title' => 'Laporan Inventory',
            'period' => $period,
            'rows' => $this->inventoryRowsForPeriod($period),
        ]);
    }

    private function inventoryRowsForPeriod(array $period): array
    {
        return Inventory::query()
            ->leftJoin('inventory_period_stocks as ps', function ($join) use ($period) {
                $join->on('ps.inventory_id', '=', 'inventories.id')
                    ->where('ps.period_year', '=', $period['year'])
                    ->where('ps.period_month', '=', $period['month']);
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
            })
            ->all();
    }

    private function resolvePeriod(Request $request): array
    {
        $now = Carbon::now();
        $year = (int) $request->input('year', $now->year);
        $month = (int) $request->input('month', $now->month);

        if ($month < 1 || $month > 12) {
            $month = $now->month;
        }

        if ($year < 2000 || $year > 2100) {
            $year = $now->year;
        }

        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = (clone $startOfMonth)->endOfMonth()->endOfDay();

        $start = $startOfMonth;
        $end = $endOfMonth;
        $defaultDate = $now->betweenIncluded($start, $end) ? $now->toDateString() : $start->toDateString();

        return [
            'year' => $year,
            'month' => $month,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'label' => $startOfMonth->translatedFormat('F Y'),
            'default_date' => $defaultDate,
            'query' => [
                'year' => $year,
                'month' => $month,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ],
        ];
    }

    private function monthOptions(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    private function getPeriodStock(Inventory $item, array $period): array
    {
        $periodStock = InventoryPeriodStock::query()
            ->where('inventory_id', $item->id)
            ->where('period_year', $period['year'])
            ->where('period_month', $period['month'])
            ->first();

        $stockAwal = $periodStock?->stock_awal ?? $item->stock_awal;
        $stockAkhir = $periodStock?->stock_akhir ?? $stockAwal;

        return [
            'record' => $periodStock,
            'stock_awal' => $stockAwal,
            'stock_akhir' => $stockAkhir,
        ];
    }

    private function firstOrCreatePeriodStock(Inventory $item, array $period): InventoryPeriodStock
    {
        return InventoryPeriodStock::firstOrCreate(
            [
                'inventory_id' => $item->id,
                'period_year' => $period['year'],
                'period_month' => $period['month'],
            ],
            [
                'stock_awal' => $item->stock_awal,
                'stock_akhir' => $item->stock_awal,
            ]
        );
    }

    private function dateMatchesPeriod(string $date, array $period): bool
    {
        $d = Carbon::parse($date);

        return ((int) $d->format('Y') === (int) $period['year'])
            && ((int) $d->format('m') === (int) $period['month']);
    }
}
