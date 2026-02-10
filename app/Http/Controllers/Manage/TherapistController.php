<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Exports\TherapistExport;
use App\Http\Requests\TherapistStoreRequest;
use App\Http\Requests\TherapistUpdateRequest;
use App\Models\TherapistCharge;
use App\Models\TherapistName;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TherapistController extends Controller
{
    public function index()
    {
        $query = TherapistCharge::query();

        $filter = request('filter', 'month');

        if ($filter === 'day' && request('date')) {
            $query->whereDate('date', request('date'));
        } elseif ($filter === 'ten_days' && request('start_date')) {
            $start = Carbon::parse(request('start_date'))->startOfDay();
            $end = (clone $start)->addDays(9)->endOfDay();
            $query->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
        } else {
            $month = (int) request('month', now()->month);
            $year = (int) request('year', now()->year);
            $query->whereMonth('date', $month)->whereYear('date', $year);
        }

        $rows = $query->orderBy('date')->orderBy('time')->paginate(25)->appends(request()->query());

        return view('manage.therapist.index', [
            'rows' => $rows,
        ]);
    }

    public function summary()
    {
        $month = (int) request('month', now()->month);
        $year = (int) request('year', now()->year);

        $base = TherapistCharge::query()
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        $totalExtraTime = (int) (clone $base)->sum('extra_time');
        $totalCustomers = (int) (clone $base)->count();

        $rows = TherapistCharge::query()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->selectRaw('therapist_name,
                SUM(traditional) as traditional,
                SUM(fullbody) as fullbody,
                SUM(butterfly) as butterfly,
                SUM(extra_time) as extra_time,
                MAX(room) as room,
                MAX(id) as last_id')
            ->groupBy('therapist_name')
            ->orderBy('therapist_name')
            ->get();

        $latestByName = TherapistCharge::query()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderByDesc('id')
            ->get()
            ->unique('therapist_name')
            ->keyBy('therapist_name');

        return view('manage.therapist.summary', [
            'rows' => $rows,
            'month' => $month,
            'year' => $year,
            'totalExtraTime' => $totalExtraTime,
            'totalCustomers' => $totalCustomers,
            'therapistNames' => TherapistName::query()->where('active', true)->orderBy('name')->get(),
            'latestByName' => $latestByName,
        ]);
    }

    public function monthly()
    {
        $month = (int) request('month', now()->month);
        $year = (int) request('year', now()->year);

        $rows = TherapistCharge::query()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->selectRaw('therapist_name,
                SUM(traditional) as traditional,
                SUM(fullbody) as fullbody,
                SUM(butterfly) as butterfly,
                SUM(extra_time) as extra_time')
            ->groupBy('therapist_name')
            ->orderBy('therapist_name')
            ->get()
            ->map(function ($row) {
                $row->total_treatment = (int) $row->traditional + (int) $row->fullbody + (int) $row->butterfly;
                return $row;
            });

        $chart = [
            'labels' => $rows->pluck('therapist_name')->all(),
            'extra_time' => $rows->pluck('extra_time')->map(fn ($v) => (int) $v)->all(),
            'total_treatment' => $rows->pluck('total_treatment')->all(),
        ];

        return view('manage.therapist.monthly', [
            'rows' => $rows,
            'month' => $month,
            'year' => $year,
            'chart' => $chart,
        ]);
    }

    public function create()
    {
        return view('manage.therapist.create', [
            'therapistNames' => TherapistName::query()->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(TherapistStoreRequest $request)
    {
        $data = $request->validated();

        $extraTime = (int) ($data['extra_time'] ?? 0);
        $traditional = (int) ($data['traditional'] ?? 0);
        $fullbody = (int) ($data['fullbody'] ?? 0);
        $butterfly = (int) ($data['butterfly'] ?? 0);
        $shockwave = (bool) ($data['shockwave'] ?? false);
        $discountPercent = (int) ($data['discount_percent'] ?? 0);
        $discountNominal = (int) ($data['discount_nominal'] ?? 0);
        $roomCharge = (int) ($data['room_charge'] ?? 0);

        $extraCharge = $extraTime * 150000;
        $packageCharge = ($traditional * 400000) + ($fullbody * 550000) + ($butterfly * 700000);
        $addOnCharge = $shockwave ? 250000 : 0;
        $subtotal = $extraCharge + $packageCharge + $addOnCharge + $roomCharge;
        $discountValue = (int) round($subtotal * ($discountPercent / 100));
        $total = max($subtotal - $discountValue - $discountNominal, 0);

        $record = TherapistCharge::create([
            'date' => $data['tanggal'],
            'time' => $data['waktu'] ?? null,
            'therapist_name' => $data['nama'],
            'extra_time' => $extraTime,
            'extra_charge' => $extraCharge,
            'traditional' => $traditional,
            'fullbody' => $fullbody,
            'butterfly' => $butterfly,
            'shockwave' => $shockwave,
            'discount_percent' => $discountPercent,
            'discount_nominal' => $discountNominal,
            'room_charge' => $roomCharge,
            'total_charge' => $total,
            'room' => $data['room'] ?? null,
        ]);

        $date = Carbon::parse($record->date);

        if ($request->input('redirect') === 'summary') {
            return redirect()
                ->route('manage.therapist.summary', [
                    'year' => $date->year,
                    'month' => $date->month,
                ])
                ->with('status', 'Therapist tersimpan.');
        }

        return redirect()
            ->route('manage.therapist.index', [
                'year' => $date->year,
                'month' => $date->month,
                'date' => $date->toDateString(),
            ])
            ->with('status', 'Therapist tersimpan.');
    }

    public function edit(int $id)
    {
        return view('manage.therapist.edit', ['id' => $id]);
    }

    public function update(TherapistUpdateRequest $request, int $id)
    {
        $data = $request->validated();

        $record = TherapistCharge::findOrFail($id);

        $extraTime = (int) ($data['extra_time'] ?? 0);
        $traditional = (int) ($data['traditional'] ?? 0);
        $fullbody = (int) ($data['fullbody'] ?? 0);
        $butterfly = (int) ($data['butterfly'] ?? 0);
        $shockwave = (bool) ($data['shockwave'] ?? false);
        $discountPercent = (int) ($data['discount_percent'] ?? 0);
        $discountNominal = (int) ($data['discount_nominal'] ?? 0);
        $roomCharge = (int) ($data['room_charge'] ?? 0);

        $extraCharge = $extraTime * 150000;
        $packageCharge = ($traditional * 400000) + ($fullbody * 550000) + ($butterfly * 700000);
        $addOnCharge = $shockwave ? 250000 : 0;
        $subtotal = $extraCharge + $packageCharge + $addOnCharge + $roomCharge;
        $discountValue = (int) round($subtotal * ($discountPercent / 100));
        $total = max($subtotal - $discountValue - $discountNominal, 0);

        $record->update([
            'date' => $data['tanggal'],
            'time' => $data['waktu'] ?? null,
            'therapist_name' => $data['nama'],
            'extra_time' => $extraTime,
            'extra_charge' => $extraCharge,
            'traditional' => $traditional,
            'fullbody' => $fullbody,
            'butterfly' => $butterfly,
            'shockwave' => $shockwave,
            'discount_percent' => $discountPercent,
            'discount_nominal' => $discountNominal,
            'room_charge' => $roomCharge,
            'total_charge' => $total,
            'room' => $data['room'] ?? null,
        ]);

        $date = Carbon::parse($record->date);
        if ($request->input('redirect') === 'summary') {
            return redirect()
                ->route('manage.therapist.summary', [
                    'year' => $date->year,
                    'month' => $date->month,
                ])
                ->with('status', 'Therapist diperbarui.');
        }

        return redirect()
            ->route('manage.therapist.index', [
                'year' => $date->year,
                'month' => $date->month,
                'date' => $date->toDateString(),
            ])
            ->with('status', 'Therapist diperbarui.');
    }

    public function destroy(int $id)
    {
        TherapistCharge::whereKey($id)->delete();

        return redirect()
            ->back()
            ->with('status', 'Therapist dihapus.');
    }

    public function exportExcel()
    {
        $mode = (string) request('view', 'detail');

        if ($mode === 'summary') {
            [$headings, $rows] = $this->summaryExportRows(request());
            return Excel::download(new TherapistExport(collect($rows), $headings), 'therapist-summary.xlsx');
        }

        if ($mode === 'monthly') {
            [$headings, $rows] = $this->monthlyExportRows(request());
            return Excel::download(new TherapistExport(collect($rows), $headings), 'therapist-monthly.xlsx');
        }

        [$headings, $rows] = $this->detailExportRows(request());
        return Excel::download(new TherapistExport(collect($rows), $headings), 'therapist.xlsx');
    }

    public function exportPdf()
    {
        $mode = (string) request('view', 'detail');

        if ($mode === 'summary') {
            [$headings, $rows, $meta] = $this->summaryPdfRows(request());
            $pdf = Pdf::loadView('manage.therapist.print', [
                'title' => 'Laporan Summary Therapist',
                'mode' => 'summary',
                'headings' => $headings,
                'rows' => $rows,
                'meta' => $meta,
            ])->setPaper('a4', 'landscape');

            return $pdf->download('therapist-summary.pdf');
        }

        if ($mode === 'monthly') {
            [$headings, $rows, $meta] = $this->monthlyPdfRows(request());
            $pdf = Pdf::loadView('manage.therapist.print', [
                'title' => 'Laporan Monthly Therapist',
                'mode' => 'monthly',
                'headings' => $headings,
                'rows' => $rows,
                'meta' => $meta,
            ])->setPaper('a4', 'landscape');

            return $pdf->download('therapist-monthly.pdf');
        }

        [$headings, $rows, $meta] = $this->detailPdfRows(request());
        $pdf = Pdf::loadView('manage.therapist.print', [
            'title' => 'Laporan Therapist',
            'mode' => 'detail',
            'headings' => $headings,
            'rows' => $rows,
            'meta' => $meta,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('therapist.pdf');
    }

    public function print()
    {
        $mode = (string) request('view', 'detail');

        if ($mode === 'summary') {
            [$headings, $rows, $meta] = $this->summaryPdfRows(request());
            return view('manage.therapist.print', [
                'title' => 'Laporan Summary Therapist',
                'mode' => 'summary',
                'headings' => $headings,
                'rows' => $rows,
                'meta' => $meta,
            ]);
        }

        if ($mode === 'monthly') {
            [$headings, $rows, $meta] = $this->monthlyPdfRows(request());
            return view('manage.therapist.print', [
                'title' => 'Laporan Monthly Therapist',
                'mode' => 'monthly',
                'headings' => $headings,
                'rows' => $rows,
                'meta' => $meta,
            ]);
        }

        [$headings, $rows, $meta] = $this->detailPdfRows(request());
        return view('manage.therapist.print', [
            'title' => 'Laporan Therapist',
            'mode' => 'detail',
            'headings' => $headings,
            'rows' => $rows,
            'meta' => $meta,
        ]);
    }

    private function therapistFilteredQuery(Request $request)
    {
        $query = TherapistCharge::query();
        $filter = $request->input('filter', 'month');

        if ($filter === 'day' && $request->filled('date')) {
            $query->whereDate('date', $request->input('date'));
        } elseif ($filter === 'ten_days' && $request->filled('start_date')) {
            $start = Carbon::parse($request->input('start_date'))->startOfDay();
            $end = (clone $start)->addDays(9)->endOfDay();
            $query->whereBetween('date', [$start->toDateString(), $end->toDateString()]);
        } else {
            $month = (int) $request->input('month', now()->month);
            $year = (int) $request->input('year', now()->year);
            $query->whereMonth('date', $month)->whereYear('date', $year);
        }

        return [$query, $filter];
    }

    private function detailExportRows(Request $request): array
    {
        [$query] = $this->therapistFilteredQuery($request);

        $rows = $query->orderBy('date')->orderBy('time')->get()->map(function ($row) {
            return [
                $row->date->format('d/m/Y'),
                $row->therapist_name,
                $row->time ?? '-',
                (int) $row->extra_time,
                (int) $row->extra_charge,
                (int) $row->traditional,
                (int) $row->fullbody,
                (int) $row->butterfly,
                $row->shockwave ? 1 : 0,
                (int) $row->discount_percent,
                (int) $row->discount_nominal,
                (int) $row->room_charge,
                (int) $row->total_charge,
                $row->room ?? '-',
            ];
        });

        $headings = [
            'Tanggal', 'Nama Therapist', 'Waktu (24 jam)', 'Extra Time (30 menit)', 'Charge Extra Time (Rp)',
            'Traditional 60', 'Fullbody 90', 'Butterfly 90', 'Add-ons Shock Wave', 'Discount (%)',
            'Discount (Rp)', 'Room Charge (Rp)', 'Total Charge (Rp)', 'Room',
        ];

        return [$headings, $rows];
    }

    private function detailPdfRows(Request $request): array
    {
        [$headings, $rows] = $this->detailExportRows($request);
        $meta = $this->exportMeta($request);

        return [$headings, $rows->all(), $meta];
    }

    private function summaryExportRows(Request $request): array
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $rows = TherapistCharge::query()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->selectRaw('therapist_name,
                SUM(traditional) as traditional,
                SUM(fullbody) as fullbody,
                SUM(butterfly) as butterfly,
                SUM(extra_time) as extra_time,
                MAX(room) as room')
            ->groupBy('therapist_name')
            ->orderBy('therapist_name')
            ->get()
            ->map(function ($row) {
                $total = (int) $row->traditional + (int) $row->fullbody + (int) $row->butterfly;
                return [
                    $row->therapist_name,
                    (int) $row->traditional,
                    (int) $row->fullbody,
                    (int) $row->butterfly,
                    (int) $row->extra_time,
                    $total,
                    $row->room ?? '-',
                ];
            });

        $headings = [
            'Nama Therapist', 'Traditional 60', 'Full Body 90', 'Butterfly 90',
            'Extra Time (30 menit)', 'Total Treatment', 'Room',
        ];

        return [$headings, $rows];
    }

    private function summaryPdfRows(Request $request): array
    {
        [$headings, $rows] = $this->summaryExportRows($request);
        $meta = [
            'period' => $this->periodLabel($request),
        ];

        return [$headings, $rows->all(), $meta];
    }

    private function monthlyExportRows(Request $request): array
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $rows = TherapistCharge::query()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->selectRaw('therapist_name,
                SUM(traditional) as traditional,
                SUM(fullbody) as fullbody,
                SUM(butterfly) as butterfly,
                SUM(extra_time) as extra_time')
            ->groupBy('therapist_name')
            ->orderBy('therapist_name')
            ->get()
            ->map(function ($row) {
                $total = (int) $row->traditional + (int) $row->fullbody + (int) $row->butterfly;
                return [
                    $row->therapist_name,
                    (int) $row->traditional,
                    (int) $row->fullbody,
                    (int) $row->butterfly,
                    (int) $row->extra_time,
                    $total,
                ];
            });

        $headings = [
            'Nama Therapist', 'Traditional 60', 'Full Body 90', 'Butterfly 90',
            'Extra Time (30 menit)', 'Total Treatment',
        ];

        return [$headings, $rows];
    }

    private function monthlyPdfRows(Request $request): array
    {
        [$headings, $rows] = $this->monthlyExportRows($request);
        $meta = [
            'period' => $this->periodLabel($request),
            'chart' => [
                'labels' => collect($rows)->pluck(0)->all(),
                'extra_time' => collect($rows)->pluck(4)->map(fn ($v) => (int) $v)->all(),
                'total_treatment' => collect($rows)->pluck(5)->map(fn ($v) => (int) $v)->all(),
            ],
        ];

        return [$headings, $rows->all(), $meta];
    }

    private function exportMeta(Request $request): array
    {
        [$query, $filter] = $this->therapistFilteredQuery($request);

        $label = match ($filter) {
            'day' => $request->filled('date') ? Carbon::parse($request->input('date'))->format('d M Y') : '-',
            'ten_days' => $request->filled('start_date')
                ? Carbon::parse($request->input('start_date'))->format('d M Y').' - '.Carbon::parse($request->input('start_date'))->addDays(9)->format('d M Y')
                : '-',
            default => $this->periodLabel($request),
        };

        return [
            'filter' => $filter,
            'label' => $label,
        ];
    }

    private function periodLabel(Request $request): string
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        return Carbon::create($year, $month, 1)->translatedFormat('F Y');
    }
}
