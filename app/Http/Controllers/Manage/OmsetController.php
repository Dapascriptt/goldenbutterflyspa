<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Exports\OmsetExport;
use App\Http\Requests\OmsetIndexRequest;
use App\Http\Requests\OmsetStoreRequest;
use App\Http\Requests\OmsetUpdateRequest;
use App\Models\Omset;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class OmsetController extends Controller
{
    public function index(OmsetIndexRequest $request)
    {
        $validated = $request->validated();
        $dateFrom = $validated['date_from'] ?? null;
        $dateTo = $validated['date_to'] ?? null;
        $search = trim((string) ($validated['q'] ?? ''));

        if (!$dateFrom && !$dateTo) {
            $dateFrom = Carbon::now()->subDays(6)->toDateString();
            $dateTo = Carbon::now()->toDateString();
        }

        if (!Schema::hasTable('omsets')) {
            $rows = new LengthAwarePaginator([], 0, 25);
        } else {
            $dateColumn = Schema::hasColumn('omsets', 'date') ? 'date' : 'created_at';
            $amountColumn = Schema::hasColumn('omsets', 'amount') ? 'amount' : (Schema::hasColumn('omsets', 'total') ? 'total' : 'amount');

            $rows = Omset::query()
                ->select([$dateColumn, 'description', 'code', 'created_by', $amountColumn.' as amount'])
                ->when($dateFrom, fn ($q) => $q->whereDate($dateColumn, '>=', $dateFrom))
                ->when($dateTo, fn ($q) => $q->whereDate($dateColumn, '<=', $dateTo))
                ->when($search !== '', function ($q) use ($search) {
                    $q->where(function ($inner) use ($search) {
                        $inner->where('description', 'like', '%'.$search.'%')
                            ->orWhere('code', 'like', '%'.$search.'%');
                    });
                })
                ->orderBy($dateColumn, 'desc')
                ->paginate(25)
                ->appends($request->query());
        }

        return view('manage.omset.index', [
            'rows' => $rows,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'q' => $search,
            ],
        ]);
    }

    public function create()
    {
        return view('manage.omset.create');
    }

    public function store(OmsetStoreRequest $request)
    {
        $request->validated();

        return redirect()
            ->route('manage.omset.index')
            ->with('status', 'Omset tersimpan (dummy).');
    }

    public function edit(int $id)
    {
        return view('manage.omset.edit', ['id' => $id]);
    }

    public function update(OmsetUpdateRequest $request, int $id)
    {
        $request->validated();

        return redirect()
            ->route('manage.omset.index')
            ->with('status', 'Omset diperbarui (dummy).');
    }

    public function destroy(int $id)
    {
        return redirect()
            ->route('manage.omset.index')
            ->with('status', 'Omset dihapus (dummy).');
    }

    public function exportExcel(OmsetIndexRequest $request)
    {
        $validated = $request->validated();
        $dateFrom = $validated['date_from'] ?? null;
        $dateTo = $validated['date_to'] ?? null;
        $search = trim((string) ($validated['q'] ?? ''));

        if (!$dateFrom && !$dateTo) {
            $dateFrom = Carbon::now()->subDays(29)->toDateString();
            $dateTo = Carbon::now()->toDateString();
        }

        if ($dateFrom && $dateTo) {
            $start = Carbon::parse($dateFrom);
            $end = Carbon::parse($dateTo);
            if ($start->diffInDays($end) > 90) {
                return redirect()
                    ->back()
                    ->withErrors(['date_to' => 'Rentang maksimal 90 hari.'])
                    ->withInput();
            }
        }

        if (!Schema::hasTable('omsets')) {
            return response()->streamDownload(function () {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Tanggal', 'Kode', 'Deskripsi', 'Nominal', 'Dibuat Oleh']);
                fclose($out);
            }, 'omset.csv');
        }

        $dateColumn = Schema::hasColumn('omsets', 'date') ? 'date' : 'created_at';
        $amountColumn = Schema::hasColumn('omsets', 'amount') ? 'amount' : (Schema::hasColumn('omsets', 'total') ? 'total' : 'amount');

        $query = Omset::query()
            ->select([
                $dateColumn,
                'code',
                'description',
                $amountColumn.' as amount',
                'created_by',
            ])
            ->when($dateFrom, fn ($q) => $q->whereDate($dateColumn, '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate($dateColumn, '<=', $dateTo))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('description', 'like', '%'.$search.'%')
                        ->orWhere('code', 'like', '%'.$search.'%');
                });
            })
            ->orderBy($dateColumn, 'desc');

        $filename = 'omset-'.Carbon::now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query, $dateColumn) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tanggal', 'Kode', 'Deskripsi', 'Nominal', 'Dibuat Oleh']);
            $query->chunk(1000, function ($rows) use ($out, $dateColumn) {
                foreach ($rows as $row) {
                    $date = $row->{$dateColumn}?->format('Y-m-d') ?? $row->{$dateColumn};
                    fputcsv($out, [
                        $date,
                        $row->code ?? '-',
                        $row->description ?? '-',
                        $row->amount ?? 0,
                        $row->created_by ?? '-',
                    ]);
                }
            });
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf()
    {
        $pdf = Pdf::loadView('manage.omset.print', [
            'title' => 'Laporan Omset',
            'rows' => OmsetExport::dummyRows(),
        ])->setPaper('a4');

        return $pdf->download('omset.pdf');
    }

    public function print()
    {
        return view('manage.omset.print', [
            'title' => 'Laporan Omset',
            'rows' => OmsetExport::dummyRows(),
        ]);
    }
}
